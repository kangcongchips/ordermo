<?php

class Order
{
    /**
     * Persist one order per merchant group in a single transaction.
     *
     * @param array $info   delivery_address, contact_phone, payment_method, notes
     * @param array $groups merchant_id => [business_name, delivery_fee, subtotal,
     *                       items => [[menu_item_id, name, price, qty, subtotal]]]
     * @return int[] created order ids
     */
    public function createFromCart(int $customerUserId, array $info, array $groups): array
    {
        $db = Database::getConnection();
        $db->beginTransaction();

        try {
            $orderStmt = $db->prepare(
                'INSERT INTO orders
                    (customer_user_id, merchant_id, delivery_address, contact_phone,
                     payment_method, subtotal, delivery_fee, total, notes)
                 VALUES (:cust, :merchant, :addr, :phone, :pay, :sub, :fee, :total, :notes)'
            );
            $itemStmt = $db->prepare(
                'INSERT INTO order_items
                    (order_id, menu_item_id, name, price, quantity, subtotal)
                 VALUES (:order, :item, :name, :price, :qty, :sub)'
            );

            $orderIds = [];

            foreach ($groups as $merchantId => $group) {
                $subtotal = (float) $group['subtotal'];
                $fee      = (float) $group['delivery_fee'];

                $orderStmt->execute([
                    ':cust'     => $customerUserId,
                    ':merchant' => (int) $merchantId,
                    ':addr'     => $info['delivery_address'],
                    ':phone'    => $info['contact_phone'],
                    ':pay'      => $info['payment_method'],
                    ':sub'      => $subtotal,
                    ':fee'      => $fee,
                    ':total'    => $subtotal + $fee,
                    ':notes'    => $info['notes'] !== '' ? $info['notes'] : null,
                ]);
                $orderId    = (int) $db->lastInsertId();
                $orderIds[] = $orderId;

                foreach ($group['items'] as $item) {
                    $itemStmt->execute([
                        ':order' => $orderId,
                        ':item'  => $item['menu_item_id'],
                        ':name'  => $item['name'],
                        ':price' => $item['price'],
                        ':qty'   => $item['qty'],
                        ':sub'   => $item['subtotal'],
                    ]);
                }
            }

            $db->commit();
            return $orderIds;
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /** An order with its merchant name and line items, or null. */
    public function findWithItems(int $id): ?array
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT o.*, m.business_name
                 FROM orders o
                 JOIN merchants m ON m.id = o.merchant_id
                 WHERE o.id = :id
                 LIMIT 1'
            );
            $stmt->execute([':id' => $id]);
            $order = $stmt->fetch();
            if (!$order) {
                return null;
            }

            $stmt = $db->prepare(
                'SELECT name, price, quantity, subtotal
                 FROM order_items WHERE order_id = :id ORDER BY id'
            );
            $stmt->execute([':id' => $id]);
            $order['items'] = $stmt->fetchAll();

            return $order;
        } catch (Throwable $e) {
            return null;
        }
    }

    public const STATUSES = [
        'pending', 'preparing', 'on_the_way', 'delivered', 'cancelled',
    ];

    /** All orders with merchant + customer name, newest first, for admin. */
    public function allWithMeta(int $limit = 0): array
    {
        try {
            $db  = Database::getConnection();
            $sql =
                'SELECT o.id, o.total, o.status, o.created_at,
                        m.business_name,
                        CONCAT(u.first_name, " ", u.last_name) AS customer_name
                 FROM orders o
                 JOIN merchants m ON m.id = o.merchant_id
                 JOIN users u ON u.id = o.customer_user_id
                 ORDER BY o.id DESC';
            if ($limit > 0) {
                $sql .= ' LIMIT ' . (int) $limit;
            }
            return $db->query($sql)->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /** One restaurant's own orders, newest first, with customer name. */
    public function forMerchant(int $merchantId, int $limit = 0): array
    {
        try {
            $db  = Database::getConnection();
            $sql =
                'SELECT o.id, o.subtotal, o.delivery_fee, o.total, o.status,
                        o.delivery_address, o.contact_phone, o.notes, o.created_at,
                        CONCAT(u.first_name, " ", u.last_name) AS customer_name
                 FROM orders o
                 JOIN users u ON u.id = o.customer_user_id
                 WHERE o.merchant_id = :m
                 ORDER BY o.id DESC';
            if ($limit > 0) {
                $sql .= ' LIMIT ' . (int) $limit;
            }
            $stmt = $db->prepare($sql);
            $stmt->execute([':m' => $merchantId]);
            return $stmt->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    /** True if the order belongs to this restaurant (ownership guard). */
    public function merchantOwns(int $orderId, int $merchantId): bool
    {
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare(
                'SELECT 1 FROM orders WHERE id = ? AND merchant_id = ? LIMIT 1'
            );
            $stmt->execute([$orderId, $merchantId]);
            return (bool) $stmt->fetchColumn();
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * Quick counters for the rider dashboard. Orders aren't assigned to a
     * specific rider, so these reflect the shared board.
     *
     * @return array{waiting:int,in_transit:int,delivered_today:int}
     */
    public function riderBoardStats(): array
    {
        try {
            $db = Database::getConnection();
            return [
                'waiting'         => (int) $db->query(
                    'SELECT COUNT(*) FROM orders WHERE status = "preparing"'
                )->fetchColumn(),
                'in_transit'      => (int) $db->query(
                    'SELECT COUNT(*) FROM orders WHERE status = "on_the_way"'
                )->fetchColumn(),
                'delivered_today' => (int) $db->query(
                    'SELECT COUNT(*) FROM orders
                     WHERE status = "delivered" AND DATE(updated_at) = CURDATE()'
                )->fetchColumn(),
            ];
        } catch (Throwable $e) {
            return ['waiting' => 0, 'in_transit' => 0, 'delivered_today' => 0];
        }
    }

    /**
     * The delivery board for riders: orders a kitchen has started or sent out,
     * oldest first (FIFO), with restaurant + drop-off details.
     */
    public function forDelivery(): array
    {
        try {
            $db = Database::getConnection();
            return $db->query(
                'SELECT o.id, o.total, o.status, o.delivery_address,
                        o.contact_phone, o.created_at,
                        m.business_name, m.business_address,
                        CONCAT(u.first_name, " ", u.last_name) AS customer_name
                 FROM orders o
                 JOIN merchants m ON m.id = o.merchant_id
                 JOIN users u ON u.id = o.customer_user_id
                 WHERE o.status IN ("preparing", "on_the_way")
                 ORDER BY o.created_at ASC'
            )->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }

    public function updateStatus(int $id, string $status): bool
    {
        if (!in_array($status, self::STATUSES, true)) {
            return false;
        }
        try {
            $db   = Database::getConnection();
            $stmt = $db->prepare('UPDATE orders SET status = ? WHERE id = ?');
            $stmt->execute([$status, $id]);
            return $stmt->rowCount() >= 0;
        } catch (Throwable $e) {
            return false;
        }
    }
}

<?php

return [
    'stock' => [
        'in_title' => 'Stock IN',
        'in_body' => ':actor added :qty :unit of :item.',
        'out_title' => 'Stock OUT',
        'out_body' => ':actor removed :qty :unit of :item.',
    ],
    'wi' => [
        'assigned_title' => 'New work instruction',
        'assigned_body' => 'You were assigned WI :number: :title.',
        'unassigned_title' => 'Work instruction reassigned',
        'unassigned_body' => 'WI :number (:title) is no longer assigned to you.',
        'deleted_title' => 'Work instruction removed',
        'deleted_body' => 'WI :number (:title) has been deleted by an administrator.',
    ],
    'wi_admin' => [
        'item_done_title' => 'Work instruction progress',
        'item_done_body' => ':worker updated an item on WI :number (:title).',
        'item_done_item_body' => ':worker completed item :item on WI :number (:title).',
        'wi_done_title' => 'Work instruction completed',
        'wi_done_body' => ':worker completed WI :number (:title).',
        'checklist_title' => 'Checklist saved',
        'checklist_body' => ':worker saved checklist progress on WI :number (:title).',
    ],
    'bell' => [
        'aria' => 'Notifications',
        'empty' => 'No notifications',
        'mark_read' => 'Mark read',
        'mark_all_read' => 'Mark all read',
    ],
];

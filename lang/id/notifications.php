<?php

return [
    'stock' => [
        'in_title' => 'Stok masuk',
        'in_body' => ':actor menambah :qty :unit :item.',
        'out_title' => 'Stok keluar',
        'out_body' => ':actor mengurangi :qty :unit :item.',
    ],
    'wi' => [
        'assigned_title' => 'Work instruction baru',
        'assigned_body' => 'Anda ditugaskan WI :number: :title.',
        'unassigned_title' => 'Work instruction dialihkan',
        'unassigned_body' => 'WI :number (:title) tidak lagi ditugaskan kepada Anda.',
        'deleted_title' => 'Work instruction dihapus',
        'deleted_body' => 'WI :number (:title) telah dihapus oleh admin.',
    ],
    'wi_admin' => [
        'item_done_title' => 'Progres work instruction',
        'item_done_body' => ':worker memperbarui item pada WI :number (:title).',
        'item_done_item_body' => ':worker menyelesaikan item :item pada WI :number (:title).',
        'wi_done_title' => 'Work instruction selesai',
        'wi_done_body' => ':worker menyelesaikan WI :number (:title).',
        'checklist_title' => 'Checklist disimpan',
        'checklist_body' => ':worker menyimpan progres checklist pada WI :number (:title).',
    ],
    'bell' => [
        'aria' => 'Notifikasi',
        'empty' => 'Tidak ada notifikasi',
        'mark_read' => 'Tandai dibaca',
        'mark_all_read' => 'Tandai semua dibaca',
    ],
];

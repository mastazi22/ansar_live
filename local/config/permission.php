<?php
return [
    'permission_list' => [
        [
            'name' => 'Personal Info',
            'url' => '#',
            'type' => 'M',
            'has_sub_menu' => true,
            'sub_menu' => [
                [
                    'name' => 'Entry',
                    'url' => 'entrylist',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => 'New Entry',
                    'url' => 'entryform',
                    'has_sub_menu' => false,
                    'type' => 'I'
                ],
                [
                    'name' => 'Reject Entry',
                    'url' => 'reject',
                    'has_sub_menu' => false,
                    'type' => 'I'
                ],
                [
                    'name' => 'Entry Verify',
                    'url' => 'entryverify',
                    'has_sub_menu' => false,
                    'type' => 'I'
                ],
                [
                    'name' => 'Edit Entry',
                    'url' => 'editEntry',
                    'has_sub_menu' => false,
                    'type' => 'I'
                ],
                [
                    'name' => 'Verify Entry(Chunk)',
                    'url' => 'chunk_verify',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => 'Draft Entry',
                    'url' => 'entrydraft',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => 'Advance Search',
                    'url' => 'entry_advanced_search',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => 'Orginal Info',
                    'url' => 'orginal_info',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => 'Print Id Card',
                    'url' => 'print_card_id_view',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],

            ]
        ],
        [
            'name' => 'Kpi Branch',
            'url' => '#',
            'type' => 'M',
            'has_sub_menu' => true,
            'sub_menu' => [
                [
                    'name' => 'Kpi Information',
                    'url' => 'kpi_view',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => 'New Kpi',
                    'url' => 'go_to_kpi_page',
                    'has_sub_menu' => false,
                    'type' => 'I'
                ],
                [
                    'name' => 'Kpi Edit',
                    'url' => 'kpi_edit',
                    'has_sub_menu' => false,
                    'type' => 'I'
                ],
                [
                    'name' => 'Kpi Verify',
                    'url' => 'kpi_verify',
                    'has_sub_menu' => false,
                    'type' => 'I'
                ],
                [
                    'name' => 'Ansar Withdraw',
                    'url' => 'ansar-withdraw-view',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => ' Ansar Before Withdraw List',
                    'url' => 'ansar_before_withdraw_view',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => 'Reduce Guard Strength',
                    'url' => 'reduce_guard_strength',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => 'Advance Search',
                    'url' => 'entry_advanced_search',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => 'Orginal Info',
                    'url' => 'orginal_info',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],
                [
                    'name' => 'Print Id Card',
                    'url' => 'print_card_id_view',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],

            ]
        ]
    ]
];
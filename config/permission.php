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
                    'name' => 'Chunk Verify',
                    'url' => 'chunk_verify',
                    'has_sub_menu' => false,
                    'type' => 'M'
                ],

            ]
        ]
    ]
];
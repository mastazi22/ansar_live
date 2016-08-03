<?php

return [
    "hrm" => [
        "Dashboard"=>"HRM",
        "Kpi Branch" => [
            "Kpi Information" => "kpi_view",
            "Ansar Withdraw" => "ansar-withdraw-view",
            "Ansar Before Withdraw List" => "ansar_before_withdraw_view",
            "Reduce Guard Strength" => "reduce_guard_strength",
            "Ansar Before Reduce List" => "ansar_before_reduce_view",
            "Kpi Withdraw" => "kpi-withdraw-view",
            "Kpi Withdraw Date Update" => "withdrawn_kpi_view",
            "Inactive Kpi List" => "inactive_kpi_view",
            "KPI Withdraw Cancel" => "kpi_withdraw_cancel_view"
        ],
        "Personal Info" => [

            "Entry" => "anser_list",
            "Verify Entry(Chunk)" => "chunk_verify",
            "Draft Entry" => "entry_draft",
            "Advance Search" => "entry_advanced_search",
            "Orginal info" => "orginal_info",
            "Print Id Card" => "print_card_id_view",

        ],
        "Service" => [
            "Panel" => "view_panel_list",
            "Offer" => "make_offer",
            "Offer Quota" => "offer_quota",
            "Embodiment" => [

                "Embodiment Entry" => "go_to_new_embodiment_page",
                "Dis-Embodiment" => "go_to_new_disembodiment_page",
                "Service Extension" => "service_extension_view",
                "Dis-Embodiment Date Correction" => "disembodiment_date_correction_view",
                "Embodiment Memorandum ID Correction" => "embodiment_memorandum_id_correction_view",
                "Freeze" => [
                    "Freeze For Dicplinary Action" => "freeze_view",
                    "After Result" => "freeze_list"

                ]

            ],
            "Black" => [
                "Add Ansar In Black List" => "blacklist_entry_view",
                "Remove Ansar From Black List" => "unblacklist_entry_view"

            ],
            "Block" => [
                "Add Ansar In Block List" => "blocklist_entry_view",
                "Remove Ansar From Block List" => "unblocklist_entry_view"

            ],
            "Ansar Transfer" => "transfer_process"
        ],
        "Report" => [
            "View Ansar In Guard" => "guard_report",
            "Ansar Transfer History" => "transfer_ansar_history",
            "Ansar Service Record" => "view_ansar_service_record",
            "View Ansar Service Report" => "ansar_service_report_view",
            "View Embodied Ansar Report" => "embodiment_report_view",
            "View Disembodied Ansar Report" => "disembodiment_report_view",
            "Blocklist Information" => "blocklist_view",
            "Blacklist Information" => "blacklist_view",
            "Three Years Over List" => "three_year_over_report_view",
            "Ansar Service Record Unit Wise" => "service_record_unitwise_view",
            "Print Transfer Letter" => "transfer_letter_view",
            "Print Embodiment Letter" => "embodiment_letter_view",
            "Print Dis-embodiment Letter" => "disembodiment_letter_view",
            "Offer Report" => "offer_report",

        ],
        "DG Forms" => [
            "Direct Offer" => "direct_offer",
            "Direct Panel" => "direct_panel_view",
            "Direct Cancel Panel" => "direct_panel_cancel_view",
            "Direct Embodiment" => "direct_embodiment",
            "Direct Dis-embodiment" => "direct_disembodiment",
            "Direct Transfer" => "direct_transfer",
            "Direct Blacklist" => [
                "Add Ansar In Black List(Direct)" => "dg_blacklist_entry_view",
                "Remove Ansar From Black List(Direct)" => "dg_unblacklist_entry_view",

            ],
            "Direct Blocklist" => [
                "Add Ansar In Block List(Direct)" => "dg_blocklist_entry_view",
                "Remove Ansar From Block List(Direct)" => "dg_unblocklist_entry_view",

            ]
        ],
        "Admin" => [
            "Global Parameter" => "global_parameter",
            "Offer Cancel" => "cancel_offer",
            "Ansar Id List" => "print_id_list",
            "Rejected Offer List" => "rejected_offer_list",

        ],
        "General Settings" => [
            "Session Information" => "view_session_list",
            "Thana Setting" => "thana_view",
            "Unit Setting" => "unit_view",
            "Disease Information" => "disease_view",
            "Skill Setting" => "skill_view",
        ]
    ]
];
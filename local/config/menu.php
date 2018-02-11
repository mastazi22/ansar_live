<?php

return [
    "hrm" => [
        "KPI Branch" => [
            "route" => "#",
            "icon" => "fa-building",
            "children" => [
                "Active KPI Information" => [
                    "route" => "kpi_view",
                    "icon" => "fa-dashboard",
                ],
                "List of Ansar Before Guard Withdraw" => [
                    "route" => "ansar_before_withdraw_view",
                    "icon" => "fa-dashboard",
                ],
                "List of Ansar Before Guard Reduce" => [
                    "route" => "ansar_before_reduce_view",
                    "icon" => "fa-dashboard",
                ],
                "Reduce Ansar In Guard Strength" => [
                    "route" => "reduce_guard_strength",
                    "icon" => "fa-dashboard",
                ],

                "Withdraw KPI" => [
                    "route" => "kpi-withdraw-view", "icon" => "fa-dashboard",
                ],
                "KPI Withdraw Cancel" => [
                    "route" => "kpi_withdraw_cancel_view",
                    "icon" => "fa-dashboard",
                ]
            ]
        ],
        "Personal Info" => [
            "route" => "#",
            "icon" => "fa-user",
            "children" => [
                "Entry" => [
                    "route" => "anser_list",
                    "icon" => "fa-dashboard",
                ],
                "Verify Entry(Chunk)" => [
                    "route" => "chunk_verify",
                    "icon" => "fa-dashboard",
                ],
                "Draft Entry" => [
                    "route" => "entry_draft",
                    "icon" => "fa-dashboard",
                ],
                "Advance Search" => [
                    "route" => "entry_advanced_search",
                    "icon" => "fa-dashboard",
                ],
                "Orginal info" => [
                    "route" => "orginal_info",
                    "icon" => "fa-dashboard",
                ],
                "View Entry info" => [
                    "route" => "entry_info",
                    "icon" => "fa-dashboard",
                ],
                "Print ID Card" => [
                    "route" => "print_card_id_view",
                    "icon" => "fa-dashboard",
                ],
            ]

        ],
        "Service" => [
            "route" => "#",
            "icon" => "fa-suitcase",
            "children" => [
                "Panel" => [
                    "route" => "view_panel_list",
                    "icon" => "fa-users",
                ],
                "Offer" => [
                    "route" => "make_offer",
                    "icon" => "fa-mobile",
                ],
                "Embodiment" => [
                    "route" => "#",
                    "icon" => "fa-dashboard",
                    "children" => [
                        "Embodiment Entry" => [
                            "route" => "go_to_new_embodiment_page",
                            "icon" => "fa-dashboard",
                        ],
                        "Dis-Embodiment" => [
                            "route" => "go_to_new_disembodiment_page",
                            "icon" => "fa-dashboard",
                        ],
                        "Service Extension" => [
                            "route" => "service_extension_view",
                            "icon" => "fa-dashboard",
                        ],
                        "Dis-Embodiment Date Correction" => [
                            "route" => "disembodiment_date_correction_view",
                            "icon" => "fa-dashboard",
                        ],
                        "Embodiment Date Correction" => [
                            "route" => "embodiment_date_correction_view",
                            "icon" => "fa-dashboard",
                        ],
                        "Embodiment Mem. ID Correction" => [
                            "route" => "embodiment_memorandum_id_correction_view",
                            "icon" => "fa-dashboard",
                        ],
                        "Disembodiment period correction" => [
                            "route" => "disembodied_period_correction",
                            "icon" => "fa-dashboard",
                        ],

                    ]

                ],
                "Freeze" => [
                    "route" => "#",
                    "icon" => "fa-dashboard",
                    "children" => [
                        "Freeze For Different Reasons" => [
                            "route" => "freeze_view",
                            "icon" => "fa-dashboard",
                        ],
                        "Freeze For Ansar Withdraw" => [
                            "route" => "ansar-withdraw-view",
                            "icon" => "fa-dashboard",
                        ],
                        "Freezed Ansar List" => [
                            "route" => "freeze_list",
                            "icon" => "fa-dashboard",
                        ]
                    ]

                ],
                "Black Listing" => [
                    "route" => "#",
                    "icon" => "fa-dashboard",
                    "children" => [
                        "Add Ansar In Black List" => [
                            "route" => "blacklist_entry_view",
                            "icon" => "fa-dashboard",
                        ],
                        "Remove Ansar From Black List" => [
                            "route" => "unblacklist_entry_view",
                            "icon" => "fa-dashboard",
                        ]
                    ]

                ],
                "Block Listing" => [
                    "route" => "#",
                    "icon" => "fa-dashboard",
                    "children" => [
                        "Add Ansar In Block List" => [
                            "route" => "blocklist_entry_view",
                            "icon" => "fa-dashboard",
                        ],
                        "Remove Ansar From Block List" => [
                            "route" => "unblocklist_entry_view",
                            "icon" => "fa-dashboard",
                        ]
                    ]

                ],
                "Ansar Transfer(Single KPI)" => [
                    "route" => "transfer_process",
                    "icon" => "fa-dashboard",
                ],
                "Ansar Transfer(Multiple KPI)" => [
                    "route" => "multiple_kpi_transfer_process",
                    "icon" => "fa-dashboard",
                ],

            ]
        ],
        "Report" => [
            "route" => "#",
            "icon" => "fa-list-alt",
            "children" => [
                "View Service Record" => [
                    "route" => "view_ansar_service_record",
                    "icon" => "fa-file-pdf-o",
                ],
                "View Previous Service Record" => [
                    "route" => "ansar_service_report_view",
                    "icon" => "fa-file-pdf-o",
                ],
                "View Ansar In Guard" => [
                    "route" => "guard_report",
                    "icon" => "fa-file-pdf-o",
                ],
                "View Ansar Transfer History" => [
                    "route" => "transfer_ansar_history",
                    "icon" => "fa-file-pdf-o",
                ],

                "Embodied Ansar Report" => [
                    "route" => "embodiment_report_view",
                    "icon" => "fa-file-pdf-o",
                ],
                "View Disembodied Report" => [
                    "route" => "disembodiment_report_view",
                    "icon" => "fa-file-pdf-o",
                ],
                "View Unfrozen Ansar Report" => [
                    "route" => "unfrozen_report",
                    "icon" => "fa-file-pdf-o",
                ],
                "Blocklist Information" => [
                    "route" => "blocklist_view",
                    "icon" => "fa-file-pdf-o",
                ],
                "Blacklist Information" => [
                    "route" => "blacklist_view",
                    "icon" => "fa-file-pdf-o",
                ],
                "3 Years Over List" => [
                    "route" => "three_year_over_report_view",
                    "icon" => "fa-file-pdf-o",
                ],
//                "Ansar Service Record Unit Wise" => [
//                    "route" => "service_record_unitwise_view",
//                    "icon" => "fa-file-pdf-o",
//                ],
                "Print Transfer Letter" => [
                    "route" => "transfer_letter_view",
                    "icon" => "fa-envelope",
                ],
                "Print Embodiment Letter" => [
                    "route" => "embodiment_letter_view",
                    "icon" => "fa-envelope",
                ],
                "Print Dis-embodiment Letter" => [
                    "route" => "disembodiment_letter_view",
                    "icon" => "fa-envelope",
                ],
                "Offer Report" => [
                    "route" => "offer_report",
                    "icon" => "fa-file-pdf-o",
                ],
            ]
        ],
        "DG Forms" => [
            "route" => "#",
            "icon" => "fa-wrench",
            "children" => [
                "Direct Offer" => [
                    "route" => "direct_offer",
                    "icon" => "fa-mobile",
                ],
                "Direct Panel" => [
                    "route" => "direct_panel_view",
                    "icon" => "fa-users",
                ],
                "Cancel Direct Panel" => [
                    "route" => "direct_panel_cancel_view",
                    "icon" => "fa-users",
                ],
                "Offer Cancel" => [
                    "route" => "cancel_offer",
                    "icon" => "fa-dashboard",
                ],
                "Direct Embodiment" => [
                    "route" => "direct_embodiment",
                    "icon" => "fa-dashboard",
                ],
                "Direct Dis-embodiment" => [
                    "route" => "direct_disembodiment",
                    "icon" => "fa-dashboard",
                ],
                "Direct Transfer" => [
                    "route" => "direct_transfer",
                    "icon" => "fa-dashboard",
                ],
                "User Action Log" => [
                    "route" => "user_action_log",
                    "icon" => "fa-file",
                ],

//                "Direct Blacklist" => [
//                    "route" => "#",
//                    "icon" => "fa-times",
//                    "children" => [
//                        "Add Ansar In Black List" => [
//                            "route" => "dg_blacklist_entry_view",
//                            "icon" => "fa-dashboard",
//                        ],
//                        "Remove Ansar From Black List" => [
//                            "route" => "dg_unblacklist_entry_view",
//                            "icon" => "fa-dashboard",
//                        ],
//                    ]
//
//                ],
//                "Direct Blocklist" => [
//                    "route" => "#",
//                    "icon" => "fa-times",
//                    "children" => [
//                        "Add Ansar In Block List" => [
//                            "route" => "dg_blocklist_entry_view",
//                            "icon" => "fa-dashboard",
//                        ],
//                        "Remove Ansar From Block List" => [
//                            "route" => "dg_unblocklist_entry_view",
//                            "icon" => "fa-dashboard",
//                        ],
//                    ]
//
//                ]
            ]
        ],
        "Admin" => [
            "route" => "#",
            "icon" => "fa-male",
            "children" => [
                "Global Parameter" => [
                    "route" => "global_parameter",
                    "icon" => "fa-dashboard",
                ],
                "System Setting" => [
                    "route" => "system_setting",
                    "icon" => "fa-cog",
                ],
                "ID Print List" => [
                    "route" => "print_id_list",
                    "icon" => "fa-dashboard",
                ],
                "Rejected Offer List" => [
                    "route" => "rejected_offer_list",
                    "icon" => "fa-dashboard",
                ],
                "Offer Quota" => [
                    "route" => "offer_quota",
                    "icon" => "fa-dashboard",
                ],
            ]

        ],
        "General Settings" => [
            "route" => "#",
            "icon" => "fa-cogs",
            "children" => [
                "Session Information" => [
                    "route" => "session_view",
                    "icon" => "fa-cog",
                ],
                "Range Setting" => [
                    "route" => "HRM.range.index",
                    "icon" => "fa-cog",
                ],
                "Unit Setting" => [
                    "route" => "HRM.unit.index",
                    "icon" => "fa-cog",
                ],
                "Thana Setting" => [
                    "route" => "thana_view",
                    "icon" => "fa-cog",
                ],
                "Disease Information" => [
                    "route" => "disease_view",
                    "icon" => "fa-cog",
                ],
                "Skill Setting" => [
                    "route" => "skill_view",
                    "icon" => "fa-cog",
                ],
            ]
        ]
    ],
    "sd" => [],
    "recruitment" => [
        "Job Category" => [
            "route" => "recruitment.category.index",
            "icon" => "fa-list"
        ],
        "Job Circular" => [
            "route" => "recruitment.circular.index",
            "icon" => "fa-clipboard"
        ],
        "Applicant Management"=>[
            "route"=>"#",
            "icon"=>"fa-user",
            "children"=>[
                "Circular Summery"=>[
                    "route"=>"recruitment.applicant.index",
                    "icon"=>"fa-circle"
                ],
                "Search Applicant"=>[
                    "route"=>"recruitment.applicant.search",
                    "icon"=>"fa-circle"
                ],
                "Edit Applicant"=>[
                    "route"=>"recruitment.applicant.info",
                    "icon"=>"fa-circle"
                ],
                "Revert Applicant Status"=>[
                    "route"=>"recruitment.applicant.revert",
                    "icon"=>"fa-circle"
                ],
                "Applicant Mark Entry"=>[
                    "route"=>"recruitment.marks.index",
                    "icon"=>"fa-circle"
                ],
                "Send SMS to Applicant"=>[
                    "route"=>"recruitment.applicant.sms",
                    "icon"=>"fa-circle"
                ],
                "Final Accepted Applicant"=>[
                    "route"=>"recruitment.applicant.final_list",
                    "icon"=>"fa-circle"
                ]

            ]
        ],
        "Reports"=>[
            "route"=>"#",
            "icon"=>"fa-file-excel-o",
            "children"=>[
                "View Applicant Status Report"=>[
                    "route"=>"report.applicants.status",
                    "icon"=>"fa-file-excel-o"
                ],
                "Download Accepted Applicant Report"=>[
                    "route"=>"report.applicants.applicat_accepted_list",
                    "icon"=>"fa-file-pdf-o"
                ],
                "Download Applicant Marks Report"=>[
                    "route"=>"report.applicants.applicat_marks_list",
                    "icon"=>"fa-file-excel-o"
                ],

            ]
        ],
        "Settings"=>[
            "route"=>"#",
            "icon"=>"fa-cog",
            "children"=>[
                "Applicant Quota"=>[
                    "route"=>"recruitment.quota.index",
                    "icon"=>"fa-cog"
                ],
                "Applicant point"=>[
                    "route"=>"recruitment.point.index",
                    "icon"=>"fa-cog"
                ],
                "Applicant editable field"=>[
                    "route"=>"recruitment.applicant.editfield",
                    "icon"=>"fa-cog"
                ],
                "Exam Center"=>[
                    "route"=>"recruitment.exam-center.index",
                    "icon"=>"fa-cog"
                ],
                "Application Instruction"=>[
                    "route"=>"recruitment.instruction",
                    "icon"=>"fa-cog"
                ],

            ]
        ],

        "Download form for HRM" => [
            "route" => "recruitment.move_to_hrm",
            "icon" => "fa-circle"
        ],
        "Edit Applicants for HRM" => [
            "route" => "recruitment.edit_for_hrm",
            "icon" => "fa-circle"
        ],
        "View Applicants Detail for HRM" => [
            "route" => "recruitment.hrm.index",
            "icon" => "fa-circle"
        ],
        "Print Applicants ID Card" => [
            "route" => "recruitment.hrm.card_print",
            "icon" => "fa-print"
        ],

    ]
];
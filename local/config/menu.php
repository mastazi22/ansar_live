<?php

return [
    "hrm" => [
        "Kpi Branch" => [
            "route" => "#",
            "icon" => "fa-users",
            "children" => [
                "Kpi Information" => [
                    "route" => "kpi_view",
                    "icon" => "fa-dashboard",
                ],
                "Ansar Withdraw" => [
                    "route" => "ansar-withdraw-view",
                    "icon" => "fa-dashboard",
                ],
                "Ansar Before Withdraw List" => [
                    "route" => "ansar_before_withdraw_view",
                    "icon" => "fa-dashboard",
                ],
                "Reduce Guard Strength" => [
                    "route" => "reduce_guard_strength",
                    "icon" => "fa-dashboard",
                ],
                "Ansar Before Reduce List" => [
                    "route" => "ansar_before_reduce_view",
                    "icon" => "fa-dashboard",
                ],
                "Kpi Withdraw" => [
                    "route" => "kpi-withdraw-view",
                    "icon" => "fa-dashboard",
                ],
                "Kpi Withdraw Date Update" => [
                    "route" => "withdrawn_kpi_view",
                    "icon" => "fa-dashboard",
                ],
                "Inactive Kpi List" => [
                    "route" => "inactive_kpi_view",
                    "icon" => "fa-dashboard",
                ],
                "KPI Withdraw Cancel" => [
                    "route" => "kpi_withdraw_cancel_view",
                    "icon" => "fa-dashboard",
                ]
            ]
        ],
        "Personal Info" => [
            "route" => "#",
            "icon" => "fa-dashboard",
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
                "Print Id Card" => [
                    "route" => "print_card_id_view",
                    "icon" => "fa-dashboard",
                ],
            ]

        ],
        "Service" => [
            "route" => "#",
            "icon" => "fa-dashboard",
            "children" => [
                "Panel" => [
                    "route" => "view_panel_list",
                    "icon" => "fa-dashboard",
                ],
                "Offer" => [
                    "route" => "make_offer",
                    "icon" => "fa-dashboard",
                ],
                "Offer Quota" => [
                    "route" => "offer_quota",
                    "icon" => "fa-dashboard",
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
                        "Embodiment Memorandum ID Correction" => [
                            "route" => "embodiment_memorandum_id_correction_view",
                            "icon" => "fa-dashboard",
                        ],
                        "Freeze" => [
                            "route" => "#",
                            "icon" => "fa-dashboard",
                            "children" => [
                                "Freeze For Dicplinary Action" => [
                                    "route" => "freeze_view",
                                    "icon" => "fa-dashboard",
                                ],
                                "After Result" => [
                                    "route" => "freeze_list",
                                    "icon" => "fa-dashboard",
                                ]
                            ]

                        ]
                    ]

                ],
                "Black" => [
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
                "Block" => [
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
                "Ansar Transfer" => [
                    "route" => "transfer_process",
                    "icon" => "fa-dashboard",
                ]
            ]
        ],
        "Report" => [
            "route" => "#",
            "icon" => "fa-dashboard",
            "children" => [
                "View Ansar In Guard" => [
                    "route" => "guard_report",
                    "icon" => "fa-dashboard",
                ],
                "Ansar Transfer History" => [
                    "route" => "transfer_ansar_history",
                    "icon" => "fa-dashboard",
                ],
                "Ansar Service Record" => [
                    "route" => "view_ansar_service_record",
                    "icon" => "fa-dashboard",
                ],
                "View Ansar Service Report" => [
                    "route" => "ansar_service_report_view",
                    "icon" => "fa-dashboard",
                ],
                "View Embodied Ansar Report" => [
                    "route" => "embodiment_report_view",
                    "icon" => "fa-dashboard",
                ],
                "View Disembodied Ansar Report" => [
                    "route" => "disembodiment_report_view",
                    "icon" => "fa-dashboard",
                ],
                "Blocklist Information" => [
                    "route" => "blocklist_view",
                    "icon" => "fa-dashboard",
                ],
                "Blacklist Information" => [
                    "route" => "blacklist_view",
                    "icon" => "fa-dashboard",
                ],
                "Three Years Over List" => [
                    "route" => "three_year_over_report_view",
                    "icon" => "fa-dashboard",
                ],
                "Ansar Service Record Unit Wise" => [
                    "route" => "service_record_unitwise_view",
                    "icon" => "fa-dashboard",
                ],
                "Print Transfer Letter" => [
                    "route" => "transfer_letter_view",
                    "icon" => "fa-dashboard",
                ],
                "Print Embodiment Letter" => [
                    "route" => "embodiment_letter_view",
                    "icon" => "fa-dashboard",
                ],
                "Print Dis-embodiment Letter" => [
                    "route" => "disembodiment_letter_view",
                    "icon" => "fa-dashboard",
                ],
                "Offer Report" => [
                    "route" => "offer_report",
                    "icon" => "fa-dashboard",
                ],
            ]
        ],
        "DG Forms" => [
            "route" => "#",
            "icon" => "fa-dashboard",
            "children" => [
                "Direct Offer" => [
                    "route" => "direct_offer",
                    "icon" => "fa-dashboard",
                ],
                "Direct Panel" => [
                    "route" => "direct_panel_view",
                    "icon" => "fa-dashboard",
                ],
                "Direct Cancel Panel" => [
                    "route" => "direct_panel_cancel_view",
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
                "Direct Blacklist" => [
                    "route" => "#",
                    "icon" => "fa-dashboard",
                    "children" => [
                        "Add Ansar In Black List" => [
                            "route" => "dg_blacklist_entry_view",
                            "icon" => "fa-dashboard",
                        ],
                        "Remove Ansar From Black List" => [
                            "route" => "dg_unblacklist_entry_view",
                            "icon" => "fa-dashboard",
                        ],
                    ]

                ],
                "Direct Blocklist" => [
                    "route" => "#",
                    "icon" => "fa-dashboard",
                    "children" => [
                        "Add Ansar In Block List" => [
                            "route" => "dg_blocklist_entry_view",
                            "icon" => "fa-dashboard",
                        ],
                        "Remove Ansar From Block List" => [
                            "route" => "dg_unblocklist_entry_view",
                            "icon" => "fa-dashboard",
                        ],
                    ]

                ]
            ]
        ],
        "Admin" => [
            "route" => "#",
            "icon" => "fa-dashboard",
            "children" => [
                "Global Parameter" => [
                    "route" => "global_parameter",
                    "icon" => "fa-dashboard",
                ],
                "Offer Cancel" => [
                    "route" => "cancel_offer",
                    "icon" => "fa-dashboard",
                ],
                "Ansar Id List" => [
                    "route" => "print_id_list",
                    "icon" => "fa-dashboard",
                ],
                "Rejected Offer List" => [
                    "route" => "rejected_offer_list",
                    "icon" => "fa-dashboard",
                ],
            ]

        ],
        "General Settings" => [
            "route" => "#",
            "icon" => "fa-dashboard",
            "children" => [
                "Session Information" => [
                    "route" => "view_session_list",
                    "icon" => "fa-dashboard",
                ],
                "Thana Setting" => [
                    "route" => "thana_view",
                    "icon" => "fa-dashboard",
                ],
                "Unit Setting" => [
                    "route" => "unit_view",
                    "icon" => "fa-dashboard",
                ],
                "Disease Information" => [
                    "route" => "disease_view",
                    "icon" => "fa-dashboard",
                ],
                "Skill Setting" => [
                    "route" => "skill_view",
                    "icon" => "fa-dashboard",
                ],
            ]
        ]
    ]
];
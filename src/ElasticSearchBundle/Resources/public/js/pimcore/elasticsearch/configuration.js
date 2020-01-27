pimcore.registerNS("saltid.elasticsearch.setting.configuration");
saltid.elasticsearch.setting.configuration = Class.create({

    initialize: function () {

        this.getData();
    },

    getData: function () {
        Ext.Ajax.request({
            url: "/saltid/elasticsearch/configuration/",
            success: function (response) {

                this.data = Ext.decode(response.responseText);

                this.getTabPanel();

            }.bind(this)
        });
    },

    getValue: function (key, ignoreCheck) {

        var nk = key.split("\.");
        var current = this.data.values;

        for (var i = 0; i < nk.length; i++) {
            if (current[nk[i]]) {
                current = current[nk[i]];
            } else {
                current = null;
                break;
            }
        }

        if (ignoreCheck || (typeof current != "object" && typeof current != "array" && typeof current != "function")) {
            return current;
        }

        return "";
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = Ext.create('Ext.panel.Panel', {
                id: "saltid_elasticsearch_configuration",
                title: t("menuConfig"),
                iconCls: "pimcore_icon_system",
                border: false,
                layout: "fit",
                closable: true
            });

            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("saltid_elasticsearch_configuration");
            }.bind(this));

            // debug
            if (this.data.values.general.debug) {
                this.data.values.general.debug = true;
            }

            this.layout = Ext.create('Ext.form.Panel', {
                bodyStyle: 'padding:20px 5px 20px 5px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                defaults: {
                    forceLayout: true
                },
                fieldDefaults: {
                    labelWidth: 250
                },
                buttons: [
                    {
                        text: t("save"),
                        handler: this.save.bind(this),
                        iconCls: "pimcore_icon_apply"
                    }
                ],
                items: [
                    {
                        xtype: 'fieldset',
                        title: t('general'),
                        collapsible: true,
                        collapsed: false,
                        autoHeight: true,
                        defaultType: 'textfield',
                        defaults: {width: 450},
                        items: [
                            {
                                fieldLabel: t('hostorip'),
                                name: 'general.hostorip',
                                xtype: "textfield",
                                emptyText: '127.0.0.1',
                                value: this.getValue("general.hostorip"),
                            },
                            {
                                fieldLabel: t('port'),
                                name: 'general.port',
                                xtype: "textfield",
                                emptyText: '9200',
                                value: this.getValue("general.port"),
                            },
                            {
                                fieldLabel: t('httpBasicAuthUser'),
                                name: 'general.httpBasicAuthUser',
                                xtype: "textfield",
                                value: this.getValue("general.httpBasicAuthUser"),
                            },
                            {
                                fieldLabel: t('httpBasicAuthPassword'),
                                name: 'general.httpBasicAuthPassword',
                                xtype: "textfield",
                                inputType: "password",
                                value: this.getValue("general.httpBasicAuthPassword"),
                            }
                        ]
                    },
                ]
            });

            this.panel.add(this.layout);

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem(this.panel);

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem("saltid_elasticsearch_configuration");
    },

    save: function () {

        this.layout.mask();

        var values = this.layout.getForm().getFieldValues();

        Ext.Ajax.request({
            url: "/saltid/elasticsearch/configuration/save",
            method: "PUT",
            params: {
                data: Ext.encode(values)
            },
            success: function (response) {

                this.layout.unmask();

                try {
                    var res = Ext.decode(response.responseText);
                    if (res.success) {
                        pimcore.helpers.showNotification(t("success"), t("saved_successfully"), "success");

                        Ext.MessageBox.confirm(t("info"), t("reload_pimcore_changes"), function (buttonValue) {
                            if (buttonValue == "yes") {
                                window.location.reload();
                            }
                        }.bind(this));
                    } else {
                        pimcore.helpers.showNotification(t("error"), t("saving_failed"),
                            "error", t(res.message));
                    }
                } catch (e) {
                    pimcore.helpers.showNotification(t("error"), t("saving_failed"), "error");
                }
            }.bind(this)
        });
    },

});

pimcore.registerNS("saltid.elasticsearch.setting.synonym");
saltid.elasticsearch.setting.synonym = Class.create({
    onFileSystem: false,
    data: {},

    initialize: function(id) {
        this.getTabPanel();
        this.load();
    },

    load: function () {
        this.panel.setLoading(true);

        Ext.Ajax.request({
            url: "/saltid/elasticsearch/synonym",
            success: function (response) {

                try {
                    var data = Ext.decode(response.responseText);
                    if(data.success) {
                        this.data = data.data;
                        this.onFileSystem = data.onFileSystem;

                        this.loadSites();
                    }
                } catch (e) {

                }
            }.bind(this)
        });
    },

    loadSites: function() {
        this.formPanel = new Ext.form.Panel({
            layout: 'fit'
        });

        var items = [];

        pimcore.globalmanager.get("sites").load(function(records) {
            Ext.each(records, function(record) {
                items.push(this.getEditPanel(record))
            }.bind(this));


            var buttons = [];

            buttons.push({
                text: t("save"),
                iconCls: "pimcore_icon_apply",
                disabled: this.onFileSystem,
                handler: this.save.bind(this)
            });

            buttons.push({
                text: "Validate",
                iconCls: "pimcore_icon_apply",
                disabled: this.onFileSystem,
                handler: function () {
                    Ext.Msg.alert('UH OH !', 'NO HANDLER YET');
                }
            })

            this.formPanel.add({
                xtype: 'tabpanel',
                layout: 'fit',
                items: items,
                buttons: buttons
            });

            this.panel.add(this.formPanel);
            this.panel.setLoading(false);
        }.bind(this));
    },

    activate: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.setActiveItem("saltid_elasticsearch_synonym");
    },

    getTabPanel: function () {

        if (!this.panel) {
            this.panel = new Ext.Panel({
                id: "saltid_elasticsearch_synonym",
                title: t('menuSynonymTokenFilter'),
                iconCls: "pimcore_nav_icon_log_admin",
                border: false,
                layout: "fit",
                closable:true,
                items: []
            });

            var tabPanel = Ext.getCmp("pimcore_panel_tabs");
            tabPanel.add(this.panel);
            tabPanel.setActiveItem("saltid_elasticsearch_synonym");


            this.panel.on("destroy", function () {
                pimcore.globalmanager.remove("synonym");
            }.bind(this));

            pimcore.layout.refresh();
        }

        return this.panel;
    },

    getEditPanel: function (siteRecord) {
        var editArea = new Ext.form.TextArea({
            xtype: "textarea",
            name: 'data['+siteRecord.get('id')+']',
            value: this.data.hasOwnProperty(siteRecord.get('id')) ? this.data[siteRecord.getId('id')] : '',
            width: "100%",
            height: "100%",
            style: "font-family: 'Courier New', Courier, monospace;",
            disabled: this.onFileSystem
        });

        var editPanel = new Ext.Panel({
            title: t('synonym'),
            layout: 'fit',
            iconCls: 'pimcore_nav_icon_log_admin',
            bodyStyle: "padding: 10px;",
            items: [editArea]
        });

        editPanel.on("bodyresize", function (el, width, height) {
            editArea.setWidth(width-20);
            editArea.setHeight(height-20);
        });

        return editPanel;
    },


    save : function () {
        Ext.Ajax.request({
            url: "/saltid/elasticsearch/synonym",
            method: "PUT",
            params: this.formPanel.form.getFieldValues(),
            success: function (response) {
                try {
                    var data = Ext.decode(response.responseText);
                    if(data.success) {
                        pimcore.helpers.showNotification(t("success"), t("saved_successfully"), "success");
                    } else {
                        throw "save error";
                    }
                } catch (e) {
                    pimcore.helpers.showNotification(t("error"), t("saving_failed"), "error");
                }
            }.bind(this)
        });
    }
});
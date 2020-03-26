pimcore.registerNS("saltid.elasticsearch.setting.index.item");
saltid.elasticsearch.setting.index.item = Class.create({

    initialize: function(parent, data) {
        this.parent = parent;
        this.data = data;

        this.currentIndex = 0;
        var panel = this.getSettings();

        this.parent.panel.add(panel);
        this.parent.panel.setActiveTab(panel);
        this.parent.panel.updateLayout();

        this.classId = 0;
        this.indexRuleId = this.data.id;

        this.firstLoad = true;

        if (!this.firstLoad) {
            this.indexConfigDialog = new saltid.elasticsearch.setting.index.config.dialog(this, null, false);
        }
    },

    getSettings: function () {
        this.settingsForm = new Ext.form.FormPanel({
            id: "pimcore_target_groups_panel_" + this.data.id,
            title: this.data.name,
            closable: true,
            deferredRender: false,
            forceLayout: true,
            bodyStyle: "padding:10px;",
            autoScroll: true,
            border:false,
            buttons: [{
                text: t("save"),
                iconCls: "pimcore_icon_apply",
                handler: this.save.bind(this)
            }],
            items: [
                {
                    xtype: 'fieldset',
                    title: t('eventListener'),
                    collapsible: true,
                    collapsed: false,
                    autoHeight: true,
                    defaultType: 'textfield',
                    defaults: {width: '100%'},
                    items: [
                        {
                            xtype: "checkbox",
                            fieldLabel: "onDataObjectPostAdd",
                            name: "onDataObjectPostAdd",
                            labelWidth: 300,
                            value: this.data.onDataObjectPostAdd,
                        },
                        {
                            xtype: "checkbox",
                            fieldLabel: "onDataObjectPostUpdate",
                            name: "onDataObjectPostUpdate",
                            labelWidth: 300,
                            value: this.data.onDataObjectPostUpdate,
                        },
                        {
                            xtype: "checkbox",
                            fieldLabel: "onDataObjectPostDelete",
                            name: "onDataObjectPostDelete",
                            labelWidth: 300,
                            value: this.data.onDataObjectPostDelete,
                        },
                        {
                            xtype: "checkbox",
                            fieldLabel: "onDataObjectPostDeleteFailure",
                            name: "onDataObjectPostDeleteFailure",
                            labelWidth: 300,
                            value: this.data.onDataObjectPostDeleteFailure,
                        }
                    ]
                },
                {
                    xtype: 'fieldset',
                    title: t('information'),
                    itemId: "fieldsetInformation",
                    collapsible: true,
                    collapsed: false,
                    autoHeight: true,
                    defaultType: 'textfield',
                    defaults: {width: '100%'},
                    items: [
                        {
                            xtype: "textfield",
                            fieldLabel: t("name"),
                            name: "name",
                            width: 350,
                            disabled: true,
                            value: this.data.name
                        },
                        {
                            xtype: "combo",
                            fieldLabel: t('className'),
                            name: "className",
                            itemId: "className",
                            width: 350,
                            valueField: "name",
                            displayField: "name",
                            fields: ['id', 'name'],
                            mode: "local",
                            triggerAction: "all",
                            value: this.data.className,
                            store: this.getPimcoreClass(),
                            listeners: {
                                select: function(a, b, c) {
                                    console.log(a);
                                    console.log(b);
                                    console.log(c);
                                },
                                afterrender: function(me, eOpts) {

                                }.bind(this),
                                change: function (me, nv, ov, eOpts) {
                                    this.firstLoad = false;
                                    this.classId = me.getSelection().getData().id;
                                }.bind(this)
                            }
                        },
                        {
                            xtype: "button",
                            text: t('classFieldSetup'),
                            name: "classField",
                            itemId: "classField",
                            width: 350,
                            handler: function () {
                                if (!this.firstLoad) {
                                    this.indexConfigDialog = new saltid.elasticsearch.setting.index.config.dialog(this, this.classId, this.indexRuleId, true);
                                }
                            }.bind(this),
                        }
                    ]
                },
                {
                    name: "active",
                    fieldLabel: t("active"),
                    xtype: "checkbox",
                    checked: this.data.active
                }
            ]
        });

        return this.settingsForm;
    },

    getPimcoreClass: function() {
        var pimcoreClass = new Ext.data.Store({
            autoDestroy: false,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: '/saltid/seoserp/class/list',
                reader: {
                    type: 'json',
                }
            }
        });

        return pimcoreClass;
    },

    getPimcoreClassFields: function(pimcoreClassId) {
        var pimcoreClassFields = new Ext.data.Store({
            autoDestroy: false,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: '/saltid/seoserp/class/fields',
                extraParams: { id: pimcoreClassId },
                reader: {
                    type: 'json',
                }
            }
        });

        return pimcoreClassFields;
    },

    save: function () {
        var saveData = {
            settings: this.settingsForm.getForm().getFieldValues()
        };

        Ext.Ajax.request({
            url: "/saltid/elasticsearch/indexrule/save",
            method: 'PUT',
            params: {
                id: this.data.id,
                data: Ext.encode(saveData)
            },
            success: function (response) {
                res = Ext.decode(response.responseText);

                if (res.success) {
                    pimcore.helpers.showNotification(t("success"), res.message, "success");
                }
                if (!res.success) {
                    pimcore.helpers.showNotification(t("warning"), res.message, "warning");
                }
                this.parent.getTree().getStore().load();
            }.bind(this)
        });
    }
});
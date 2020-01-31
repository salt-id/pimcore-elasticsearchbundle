pimcore.registerNS("saltid.elasticsearch.setting.indexPanel");
saltid.elasticsearch.setting.indexPanel = Class.create({
    initialize: function() {
        this.treeDataUrl = '/saltid/elasticsearch/indexrule/list';
    },

    getLayout: function () {
        if (this.layout == null) {
            this.layout = new Ext.Panel({
                title: t('elasticsearchIndex'),
                layout: "border",
                closable: true,
                border: false,
                iconCls: "pimcore_nav_icon_class",
                items: [this.getTree(), this.getTabPanel()]
            });
        }

        return this.layout;
    },

    getTree: function () {
        if (!this.tree) {
            var store = Ext.create('Ext.data.TreeStore', {
                autoLoad: false,
                autoSync: true,
                proxy: {
                    type: 'ajax',
                    url: this.treeDataUrl,
                    reader: {
                        type: 'json'
                    }
                }
            });

            this.tree = new Ext.tree.TreePanel({
                store: store,
                region: "west",
                autoScroll:true,
                animate:false,
                containerScroll: true,
                width: 200,
                split: true,
                root: {
                    id: '0'
                },
                listeners: this.getTreeNodeListeners(),
                rootVisible: false,
                tbar: {
                    cls: 'pimcore_toolbar_border_bottom',
                    items: [
                        {
                            text: t("add"),
                            iconCls: "pimcore_icon_add",
                            handler: this.addIndexRule.bind(this)
                        }
                    ]
                }
            });

        }

        return this.tree;
    },

    getTreeNodeListeners: function () {
        var treeNodeListeners = {
            'itemclick': this.onTreeNodeClick.bind(this),
            "itemcontextmenu": this.onTreeNodeContextmenu.bind(this),
            "render": function () {
                this.getRootNode().expand();
            },
            'beforeitemappend': function (thisNode, newChildNode, index, eOpts) {
                var classes = [];
                var iconClasses = ['pimcore_nav_icon_class'];

                if (!newChildNode.data.active) {
                    classes.push('pimcore_unpublished');
                }

                //newChildNode.data.expanded = true;
                newChildNode.data.leaf = true;
                newChildNode.data.cls = classes.join(' ');
                newChildNode.data.iconCls = iconClasses.join(' ');
            }
        };
        return treeNodeListeners;
    },


    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts ) {
        tree.select();

        var menu = new Ext.menu.Menu();
        menu.add(new Ext.menu.Item({
            text: t('delete'),
            iconCls: "pimcore_icon_delete",
            handler: this.deleteIndexRule.bind(this, tree, record)
        }));

        e.stopEvent();
        menu.showAt(e.pageX, e.pageY);
    },

    addIndexRule: function () {
        Ext.MessageBox.prompt(' ', t('enter_the_name_of_the_new_item'),
            this.addIndexRuleComplete.bind(this), null, null, "");
    },

    onTreeNodeClick: function (tree, record, item, index, e, eOpts ) {
        this.openIndexRule(record.data);
    },

    addIndexRuleComplete: function (button, value, object) {

        if (button == "ok" && value.length > 2) {
            Ext.Ajax.request({
                url: "/saltid/elasticsearch/indexrule/add",
                method: 'POST',
                params: {
                    name: value
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    this.tree.getStore().reload();

                    if(!data || !data.success) {
                        Ext.Msg.alert(' ', data.message);
                    } else {
                        this.openIndexRule(intval(data.id));

                        pimcore.globalmanager.get("target_group_store").reload();
                    }
                }.bind(this)
            });
        } else if (button == "cancel") {
            return;
        }
        else {
            Ext.Msg.alert(' ', t('naming_requirements_3chars'));
        }
    },

    deleteIndexRule: function (tree, record) {
        Ext.Ajax.request({
            url: "/saltid/elasticsearch/indexrule/delete",
            method: 'DELETE',
            params: {
                id: record.data.id
            },
            success: function () {
                this.tree.getStore().load();

                pimcore.globalmanager.get("target_group_store").reload();
            }.bind(this)
        });
    },

    openIndexRule: function (node) {
        if(!is_numeric(node)) {
            node = node.id;
        }

        var existingPanel = Ext.getCmp("pimcore_target_groups_panel_" + node);
        if(existingPanel) {
            this.panel.setActiveItem(existingPanel);
            return;
        }

        Ext.Ajax.request({
            url: "/saltid/elasticsearch/indexrule/get",
            params: {
                id: node
            },
            success: function (response) {
                var res = Ext.decode(response.responseText);
                var item = new saltid.elasticsearch.setting.index.item(this, res);
            }.bind(this)
        });

    },

    getTabPanel: function () {
        if (!this.panel) {
            this.panel = new Ext.TabPanel({
                region: "center",
                border: false,
                plugins:
                    [
                        Ext.create('Ext.ux.TabCloseMenu', {
                            showCloseAFll: true,
                            showCloseOthers: true
                        }),
                        Ext.create('Ext.ux.TabReorderer', {})
                    ]
            });
        }

        return this.panel;
    }
});

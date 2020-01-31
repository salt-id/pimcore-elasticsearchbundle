pimcore.registerNS("saltid.elasticsearch.setting.index.config.dialog");
saltid.elasticsearch.setting.index.config.dialog = Class.create({

    initialize: function (obj, classId, indexRuleId, showWindow = false) {
        this.classId = classId;
        this.indexRuleId = indexRuleId;

        console.log("Nyoh tak kei indexRuleId gek diolah : " + this.indexRuleId);

        this.configPanel = new Ext.Panel({
            layout: "border",
            iconCls: "pimcore_icon_table",
            title: t("classFieldSetup"),
            items: [this.getSelectionPanel(), this.getLeftPanel()]
        });

        var tabs = [this.configPanel];

        this.tabPanel = new Ext.TabPanel({
            activeTab: 0,
            forceLayout: true,
            items: tabs
        });

        buttons = [];

        buttons.push({
            text: t("apply"),
            iconCls: "pimcore_icon_apply",
            handler: function () {
                this.commitData();
            }.bind(this)
        });

        this.window = new Ext.Window({
            width: 950,
            height: '95%',
            modal: true,
            title: t('indexConfig'),
            layout: "fit",
            items: [this.tabPanel],
            buttons: buttons
        });

        if (showWindow === true) {
            this.window.show();
        }
    },

    getSelectionPanel: function () {
        if (!this.selectionPanel) {

            var childs = [];

            this.cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
                clicksToEdit: 1
            });

            var store = new Ext.data.TreeStore({
                fields: [{
                    name: "text"
                }, {
                    name: "preview",
                    persist: false
                }

                ],
                root: {
                    id: "0",
                    root: true,
                    text: t("selected_grid_columns"),
                    leaf: false,
                    isTarget: true,
                    expanded: true,
                    children: childs
                }
            });

            var columns = [
                {
                    xtype: 'treecolumn',
                    text: t('configuration'),
                    dataIndex: 'text',
                    flex: 90
                }
            ];

            this.selectionPanel = new Ext.tree.TreePanel({
                store: store,
                plugins: [this.cellEditing],
                rootVisible: false,
                viewConfig: {
                    plugins: {
                        ptype: 'treeviewdragdrop',
                        ddGroup: "columnconfigelement"
                    },
                    listeners: {

                    },
                },
                id: 'tree',
                region: 'east',
                title: t('selected_grid_columns'),
                layout: 'fit',
                width: 640,
                split: true,
                autoScroll: true,
                rowLines: true,
                columnLines: true,
                listeners: {
                    itemcontextmenu: this.onTreeNodeContextmenu.bind(this)
                },
                columns: columns
            });
            var store = this.selectionPanel.getStore();
            var model = store.getModel();
            model.setProxy({
                type: 'memory'
            });
        }

        return this.selectionPanel;
    },

    getLeftPanel: function() {
        if (!this.leftPanel) {

            //var items = this.getOperatorTrees();
            //items.unshift(this.getClassDefinitionTreePanel());

            var items = this.getClassDefinitionTreePanel();

            this.brickKeys = [];
            this.leftPanel = new Ext.Panel({
                cls: "pimcore_panel_tree pimcore_gridconfig_leftpanel",
                region: "center",
                split: true,
                width: 300,
                minSize: 175,
                collapsible: true,
                collapseMode: 'header',
                collapsed: false,
                animCollapse: false,
                layout: 'accordion',
                hideCollapseTool: true,
                header: false,
                layoutConfig: {
                    animate: false
                },
                hideMode: "offsets",
                items: items
            });
        }

        return this.leftPanel;
    },

    updatePreview: function () {
        if (this.previewSettings && this.previewSettings.allowPreview) {
            this.commitData(false, true);
        }
    },

    doGetRecursiveData: function (node) {
        var childs = [];
        node.eachChild(function (child) {
            var attributes = child.data.configAttributes;
            attributes.childs = this.doGetRecursiveData(child);
            childs.push(attributes);
        }.bind(this));

        return childs;
    },

    commitData: function (save, preview) {
        this.data = {};

        if (this.selectionPanel) {
            this.data.columns = [];
            this.selectionPanel.getRootNode().eachChild(function (child) {
                var obj = {};

                if (child.data.isOperator) {
                    var attributes = child.data.configAttributes;
                    var operatorChilds = this.doGetRecursiveData(child);
                    attributes.childs = operatorChilds;
                    operatorFound = true;

                    obj.isOperator = true;
                    obj.attributes = attributes;

                } else {
                    obj.key = child.data.key;
                    obj.label = child.data.layout ? child.data.layout.title : child.data.text;
                    obj.type = child.data.dataType;
                    obj.layout = child.data.layout;
                    if (child.data.width) {
                        obj.width = child.data.width;
                    }
                }

                this.data.columns.push(obj);
            }.bind(this));
        }

        var columnsPostData = Ext.encode(this.data.columns);

        console.log(columnsPostData);

        Ext.Ajax.request({
            url: "/saltid/elasticsearch/indexrule/classfieldsetup/" + this.indexRuleId,
            method: 'PUT',
            params: {
                id: this.data.id,
                data: columnsPostData
            },
            success: function (response) {
                res = Ext.decode(response.responseText);

                if (res.success) {
                    pimcore.helpers.showNotification(t("success"), res.message, "success");
                }
                if (!res.success) {
                    pimcore.helpers.showNotification(t("warning"), res.message, "warning");
                }

            }.bind(this)
        });

        this.window.close();
    },

    onTreeNodeContextmenu: function (tree, record, item, index, e, eOpts) {
        e.stopEvent();

        tree.select();

        var menu = new Ext.menu.Menu();

        if (this.id != 0) {
            menu.add(new Ext.menu.Item({
                text: t('delete'),
                iconCls: "pimcore_icon_delete",
                handler: function (node) {
                    record.parentNode.removeChild(record, true);
                }.bind(this, record)
            }));

            if (record.data.children && record.data.children.length > 0) {
                menu.add(new Ext.menu.Item({
                    text: t('collapse_children'),
                    iconCls: "pimcore_icon_collapse_children",
                    handler: function (node) {
                        record.collapseChildren();
                    }.bind(this, record)
                }));

                menu.add(new Ext.menu.Item({
                    text: t('expand_children'),
                    iconCls: "pimcore_icon_expand_children",
                    handler: function (node) {
                        record.expandChildren();
                    }.bind(this, record)
                }));
            }

            if (record.data.isOperator) {
                menu.add(new Ext.menu.Item({
                    text: t('edit'),
                    iconCls: "pimcore_icon_edit",
                    handler: function (node) {
                        this.getConfigElement(node.data.configAttributes).getConfigDialog(node,
                            {
                                callback: this.updatePreview.bind(this)
                            });
                    }.bind(this, record)
                }));
            }
        }

        menu.showAt(e.pageX, e.pageY);
    },

    getClassDefinitionTreePanel: function () {
        if (!this.classDefinitionTreePanel) {

            var items = [];

            this.brickKeys = [];
            this.classDefinitionTreePanel = this.getClassTree("/admin/class/get-class-definition-for-column-config",
                this.classId, null);
        }

        return this.classDefinitionTreePanel;
    },

    getClassTree: function (url, classId, objectId) {

        var classTreeHelper = new pimcore.object.helpers.classTree(this.showFieldname);
        var tree = classTreeHelper.getClassTree(url, classId, objectId);

        tree.addListener("itemdblclick", function (tree, record, item, index, e, eOpts) {
            if (!record.data.root && record.data.type != "layout"
                && record.data.dataType != 'localizedfields') {
                var copy = Ext.apply({}, record.data);

                if (this.selectionPanel && !this.selectionPanel.getRootNode().findChild("key", record.data.key)) {
                    delete copy.id;
                    copy = this.selectionPanel.getRootNode().appendChild(copy);

                    var ownerTree = this.selectionPanel;

                    if (record.data.dataType == "classificationstore") {
                        var ccd = new pimcore.object.classificationstore.columnConfigDialog();
                        ccd.getConfigDialog(ownerTree, copy, this.selectionPanel);
                    } else {
                        this.updatePreview();
                    }
                }
            }
        }.bind(this));

        return tree;
    },
});
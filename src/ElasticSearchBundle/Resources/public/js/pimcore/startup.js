pimcore.registerNS("pimcore.plugin.ElasticSearchBundle");

pimcore.plugin.ElasticSearchBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.ElasticSearchBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        var elasticsearchMenu = [];

        elasticsearchMenu.push({
            text: t('menuConfig'),
            iconCls: "pimcore_nav_icon_system_settings",
            handler: this.elasticsearchMenuConfigHandler
        });

        elasticsearchMenu.push({
            text: t('menuSynonymTokenFilter'),
            iconCls: "pimcore_nav_icon_log_admin",
            handler: this.elasticsearchMenuSynonymTokenFilterHandler
        });

        elasticsearchMenu.push({
            text: t('elasticsearchIndex'),
            iconCls: "pimcore_nav_icon_class",
            handler: this.elasticsearchMenuIndexHandler
        })

        var extensionManagerMenu = new Ext.Action({
            text: t('elasticsearch'),
            iconCls: 'elasticsearch_icon',
            menu: {
                cls: "pimcore_navigation_flyout",
                shadow: false,
                items: elasticsearchMenu
            }
        });

        layoutToolbar.extensionManagerMenu.add(extensionManagerMenu);
    },

    elasticsearchMenuConfigHandler: function () {
        //alert("Oke deh !");

        //Ext.Msg.alert('Oh Yeah !', 'Ready to go!');

        try {
            pimcore.globalmanager.get("saltid_elasticsearch_configuration").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("saltid_elasticsearch_configuration", new saltid.elasticsearch.setting.configuration());
        }
    },

    elasticsearchMenuSynonymTokenFilterHandler: function () {
        try {
            pimcore.globalmanager.get("synonym").activate();
        }
        catch (e) {
            pimcore.globalmanager.add("synonym", new saltid.elasticsearch.setting.synonym());
        }
    },

    elasticsearchMenuIndexHandler: function () {
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        try {
            tabPanel.setActiveTab(pimcore.globalmanager.get("saltid_elasticsearch_index_panel").getLayout());
        }
        catch (e) {
            var elasticsearchIndexPanel = new saltid.elasticsearch.setting.indexPanel();
            pimcore.globalmanager.add("saltid_elasticsearch_index_panel", elasticsearchIndexPanel);

            tabPanel.add(elasticsearchIndexPanel.getLayout());
            tabPanel.setActiveTab(elasticsearchIndexPanel.getLayout());

            elasticsearchIndexPanel.getLayout().on("destroy", function () {
                pimcore.globalmanager.remove("saltid_elasticsearch_index_panel");
            }.bind(this));

            pimcore.layout.refresh();
        }
    },
});

var ElasticSearchBundlePlugin = new pimcore.plugin.ElasticSearchBundle();

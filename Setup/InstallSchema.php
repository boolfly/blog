<?php
namespace Boolfly\Blog\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->createCategoryTable($setup);
        $this->createPostTable($setup);
        $this->createAuthorTable($setup);
        $this->createTagTable($setup);
        $this->createPostCategoryTable($setup);
        $this->createTagPostTable($setup);
        $this->createPostProductTable($setup);
        $this->createCategoryStoreTable($setup);
        $this->createPostStoreTable($setup);
        $setup->endSetup();
    }

    /**
     * Creat bf_blog_post_store table
     *
     * @param $setup
     */
    protected function createPostStoreTable($setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('bf_blog_post_store')
        )->addColumn(
            'post_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'primary' => true],
            'Post ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $setup->getIdxName('bf_blog_post_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName('bf_blog_post_store', 'post_id', 'bf_blog_post', 'post_id'),
            'post_id',
            $setup->getTable('bf_blog_post'),
            'post_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('bf_blog_post_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Boolfly Blog Post To Store Linkage Table'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Create bf_blog_category_store table
     *
     * @param $setup
     */
    protected function createCategoryStoreTable($setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('bf_blog_category_store')
        )->addColumn(
            'category_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $setup->getIdxName('bf_blog_category_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $setup->getFkName('bf_blog_category_store', 'category_id', 'bf_blog_category', 'category_id'),
            'category_id',
            $setup->getTable('bf_blog_category'),
            'category_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('bf_blog_category_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Boolfly Blog Category To Store Linkage Table'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Create bf_blog_post_product table
     *
     * @param $setup
     */
    protected function createPostProductTable($setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('bf_blog_post_product')
        )->addColumn(
            'post_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'primary' => true],
            'Post ID'
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            11,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Product ID'
        )->setComment(
            'Boolfly Blog Post To Product Linkage Table'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     *  Create bf_blog_tag_post table
     *
     * @param $setup
     */
    protected function createTagPostTable($setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('bf_blog_tag_post')
        )->addColumn(
            'post_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'primary' => true],
            'Post ID'
        )->addColumn(
            'tag_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'primary' => true],
            'Tag ID'
        )->addIndex(
            $setup->getIdxName('bf_blog_tag_post', ['tag_id']),
            ['tag_id']
        )->addForeignKey(
            $setup->getFkName('bf_blog_tag_post', 'post_id', 'bf_blog_post', 'post_id'),
            'post_id',
            $setup->getTable('bf_blog_post'),
            'post_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('bf_blog_tag_post', 'tag_id', 'bf_blog_tag', 'tag_id'),
            'tag_id',
            $setup->getTable('bf_blog_tag'),
            'tag_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Boolfly Blog Post To Tag Linkage Table'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Create bf_blog_post_category table
     *
     * @param $setup
     */
    protected function createPostCategoryTable($setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('bf_blog_post_category')
        )->addColumn(
            'post_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false, 'primary' => true],
            'Post ID'
        )->addColumn(
            'category_id',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'primary' => true],
            'Category ID'
        )->addIndex(
            $setup->getIdxName('bf_blog_post_category', ['category_id']),
            ['category_id']
        )->addForeignKey(
            $setup->getFkName('bf_blog_post_category', 'post_id', 'bf_blog_post', 'post_id'),
            'post_id',
            $setup->getTable('bf_blog_post'),
            'post_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $setup->getFkName('bf_blog_post_category', 'category_id', 'bf_blog_category', 'category_id'),
            'category_id',
            $setup->getTable('bf_blog_category'),
            'category_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Boolfly Blog Post To Category Linkage Table'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Create bf_blog_tag table
     *
     * @param $setup
     */
    protected function createTagTable($setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('bf_blog_tag')
        )->addColumn(
            'tag_id',
            Table::TYPE_INTEGER,
            11,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Tag ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Tag Name'
        )->addColumn(
            'url_key',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'URL Key'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Tag Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Tag Modification Time'
        )->addIndex(
            $setup->getIdxName(
                $setup->getTable('bf_blog_tag'),
                ['name'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['name'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Boolfly Blog Tag Table'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Create bf_blog_author table
     *
     * @param $setup
     */
    protected function createAuthorTable($setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('bf_blog_author')
        )->addColumn(
            'author_id',
            Table::TYPE_INTEGER,
            11,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Author ID'
        )->addColumn(
            'first_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Author Firstname'
        )->addColumn(
            'last_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Author Lastname'
        )->addColumn(
            'image',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Profile Image'
        )->addColumn(
            'url_key',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'URL Key'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Description'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Author Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Author Modification Time'
        )->addIndex(
            $setup->getIdxName(
                $setup->getTable('bf_blog_author'),
                ['first_name'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['first_name'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Boolfly Blog Author Table'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Create bf_blog_post table
     *
     * @param $setup
     */
    protected function createPostTable($setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('bf_blog_post')
        )->addColumn(
            'post_id',
            Table::TYPE_INTEGER,
            11,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Post ID'
        )->addColumn(
            'title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Post Title'
        )->addColumn(
            'url_key',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'URL Key'
        )->addColumn(
            'image',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Image'
        )->addColumn(
            'short_content',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Short Content'
        )->addColumn(
            'content',
            Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Content'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Is Post Active'
        )->addColumn(
            'meta_title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Page Meta Keywords'
        )->addColumn(
            'meta_keywords',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Page Meta Keywords'
        )->addColumn(
            'meta_description',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Page Meta Description'
        )->addColumn(
            'author_id',
            Table::TYPE_INTEGER,
            11,
            ['nullable' => false],
            'Author ID'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Post Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Post Modification Time'
        )->addIndex(
            $setup->getIdxName(
                $setup->getTable('bf_blog_post'),
                ['title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->addForeignKey(
            $setup->getFkName('bf_blog_post', 'author_id', 'bf_blog_author', 'author_id'),
            'author_id',
            $setup->getTable('bf_blog_author'),
            'author_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Boolfly Blog Post Table'
        );
        $setup->getConnection()->createTable($table);
    }

    /**
     * Create bf_blog_category table
     *
     * @param $setup
     */
    protected function createCategoryTable($setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable('bf_blog_category')
        )->addColumn(
            'category_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Category Name'
        )->addColumn(
            'url_key',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'URL Key'
        )->addColumn(
            'is_active',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Is Category Active'
        )->addColumn(
            'meta_title',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Page Meta Title'
        )->addColumn(
            'meta_keywords',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Page Meta Keywords'
        )->addColumn(
            'meta_description',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Page Meta Description'
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT],
            'Category Creation Time'
        )->addColumn(
            'update_time',
            Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => Table::TIMESTAMP_INIT_UPDATE],
            'Category Modification Time'
        )->addIndex(
            $setup->getIdxName(
                $setup->getTable('bf_blog_category'),
                ['name'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['name'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Boolfly Blog Category Table'
        );
        $setup->getConnection()->createTable($table);
    }
}

<?php

namespace Boolfly\Blog\Controller;

use Boolfly\Blog\Model\AuthorFactory;
use Boolfly\Blog\Model\CategoryFactory;
use Boolfly\Blog\Model\Config\Config;
use Boolfly\Blog\Model\PostFactory;
use Boolfly\Blog\Model\TagFactory;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Url;

/**
 * Class Router
 */
class Router implements RouterInterface
{
    const AUTHOR_PREFIX = '/author/';
    const TAG_PREFIX = '/tag/';

    /**
     * @var ActionFactory
     */
    private $actionFactory;

    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var TagFactory
     */
    protected $tagFactory;

    /**
     * @var AuthorFactory
     */
    protected $authorFactory;

    /**
     * Router constructor.
     *
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     * @param Config $config
     * @param PostFactory $postFactory
     * @param CategoryFactory $categoryFactory
     * @param TagFactory $tagFactory
     * @param AuthorFactory $authorFactory
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response,
        Config $config,
        PostFactory $postFactory,
        CategoryFactory $categoryFactory,
        TagFactory $tagFactory,
        AuthorFactory $authorFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
        $this->config = $config;
        $this->postFactory = $postFactory;
        $this->categoryFactory = $categoryFactory;
        $this->tagFactory = $tagFactory;
        $this->authorFactory = $authorFactory;
    }

    /**
     * @param RequestInterface $request
     * @return ActionInterface|null
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function match(RequestInterface $request)
    {
        if (!$this->config->isEnabled()) {
            return null;
        }
        $identifier = trim($request->getPathInfo(), '/');
        $explodedIdentifier = explode('/', $identifier);
        $urlKey = $this->config->getRouter();
        if ($urlKey) {
            if ($explodedIdentifier[0] == $urlKey && !isset($explodedIdentifier[1])) {
                $request->setModuleName('bf_blog');
                $request->setControllerName('index');
                $request->setActionName('index');
                $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey);

                return $this->actionFactory->create(Forward::class);
            }

            $post = $this->postFactory->create();
            $category = $this->categoryFactory->create();
            $tag = $this->tagFactory->create();
            $author = $this->authorFactory->create();

            if (isset($explodedIdentifier[1])) {
                $postId = $post->checkUrlKey($explodedIdentifier[1]);
                $categoryId = $category->checkUrlKey($explodedIdentifier[1]);
                if (isset($explodedIdentifier[2])) {
                    if ($explodedIdentifier[1] == 'tag') {
                        $tagId = $tag->checkUrlKey($explodedIdentifier[2]);
                    }
                    if ($explodedIdentifier[1] == 'author') {
                        $authorId = $author->checkUrlKey($explodedIdentifier[2]);
                    }
                }
            }

            if ($explodedIdentifier[0] == $urlKey || $explodedIdentifier[0] == 'bf_blog') {
                if (!empty($postId)) {
                    $request->setModuleName('bf_blog');
                    $request->setControllerName('post');
                    $request->setActionName('view');
                    $request->setParam('id', $postId);

                    return $this->actionFactory->create(Forward::class);
                }

                if (!empty($categoryId)) {
                    $request->setModuleName('bf_blog');
                    $request->setControllerName('category');
                    $request->setActionName('view');
                    $request->setParam('id', $categoryId);
                    $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey . '/' . $explodedIdentifier[1]);

                    return $this->actionFactory->create(Forward::class);
                }

                if (!empty($tagId)) {
                    $request->setModuleName('bf_blog');
                    $request->setControllerName('tag');
                    $request->setActionName('view');
                    $request->setParam('id', $tagId);
                    $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey . self::TAG_PREFIX . $explodedIdentifier[2]);

                    return $this->actionFactory->create(Forward::class);
                }

                if (!empty($authorId)) {
                    $request->setModuleName('bf_blog');
                    $request->setControllerName('author');
                    $request->setActionName('view');
                    $request->setParam('id', $authorId);
                    $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $urlKey . self::AUTHOR_PREFIX . $explodedIdentifier[2]);

                    return $this->actionFactory->create(Forward::class);
                }

                return null;
            }
        }
    }
}

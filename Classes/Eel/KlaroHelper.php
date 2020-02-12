<?php

namespace Sandstorm\CookieConsent\Eel;

/*
 * This file is part of the Neos.Seo package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Eel\ProtectedContextAwareInterface;
use Neos\Media\Domain\Model\AssetInterface;
use Neos\Media\Domain\Model\ImageInterface;
use Neos\Media\Domain\Model\ThumbnailConfiguration;
use Neos\Media\Domain\Service\AssetService;
use Neos\Media\Domain\Service\ThumbnailService;

class KlaroHelper implements ProtectedContextAwareInterface
{

    /**
     * @param NodeInterface[] $apps
     * @return array
     */
    public function createAppConfiguration(array $apps)
    {
        return array_map(function($app) {
            /** @var NodeInterface $app */
            return array_merge((array)$app->getProperties(), $app->getNodeType()->getFullConfiguration()['klaro']);
        }, $apps);
    }

    /**
     * All methods are considered safe
     *
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}

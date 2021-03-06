<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Security;

use c975L\ConfigBundle\Service\ConfigServiceInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter for GiftVoucherPurchased access
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class PurchasedVoter extends Voter
{
    /**
     * Stores ConfigServiceInterface
     * @var ConfigServiceInterface
     */
    private $configService;

    /**
     * Stores AccessDecisionManagerInterface
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * Used for access to utilisation
     * @var string
     */
    public const UTILISATION = 'c975LGiftVoucher-utilisation';

    /**
     * Used for access to utilisation
     * @var string
     */
    public const UTILISATION_CONFIRM = 'c975LGiftVoucher-utilisation-confirm';

    /**
     * Contains all the available attributes to check with in supports()
     * @var array
     */
    private const ATTRIBUTES = array(
        self::UTILISATION,
        self::UTILISATION_CONFIRM,
    );

    public function __construct(
        ConfigServiceInterface $configService,
        AccessDecisionManagerInterface $decisionManager
    )
    {
        $this->configService = $configService;
        $this->decisionManager = $decisionManager;
    }

    /**
     * Checks if attribute and subject are supported
     * @return bool
     */
    protected function supports($attribute, $subject)
    {
        if (null !== $subject) {
            return $subject instanceof GiftVoucherPurchased && in_array($attribute, self::ATTRIBUTES);
        }

        return in_array($attribute, self::ATTRIBUTES);
    }

    /**
     * Votes if access is granted
     * @return bool
     * @throws LogicException
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        //Defines access rights
        switch ($attribute) {
            case self::UTILISATION:
            case self::UTILISATION_CONFIRM:
                return $this->decisionManager->decide($token, array($this->configService->getParameter('c975LGiftVoucher.roleNeeded', 'c975l/giftvoucher-bundle')));
                break;
        }

        throw new LogicException('Invalid attribute: ' . $attribute);
    }
}
<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Security;

use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;

/**
 * Voter for GiftVoucherPurchased access
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class PurchasedVoter extends Voter
{
    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;

    /**
     * The role needed to be allowed access (defined in config)
     * @var string
     */
    private $roleNeeded;

    /**
     * Used for access to utilisation
     * @var string
     */
    public const UTILISATION = 'utilisation';

    /**
     * Used for access to utilisation
     * @var string
     */
    public const UTILISATION_CONFIRM = 'utilisation-confirm';

    /**
     * Contains all the available attributes to check with in supports()
     * @var array
     */
    private const ATTRIBUTES = array(
        self::UTILISATION,
        self::UTILISATION_CONFIRM,
    );

    public function __construct(AccessDecisionManagerInterface $decisionManager, string $roleNeeded)
    {
        $this->decisionManager = $decisionManager;
        $this->roleNeeded = $roleNeeded;
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
     * @throws \LogicException
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        //Defines access rights
        switch ($attribute) {
            case self::UTILISATION:
            case self::UTILISATION_CONFIRM:
                return $this->decisionManager->decide($token, array($this->roleNeeded));
        }

        throw new \LogicException('Invalid attribute: ' . $attribute);
    }
}
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
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;

/**
 * Voter for GiftVoucherAvailable access
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class AvailableVoter extends Voter
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
     * Used for access to create
     * @var string
     */
    public const CREATE = 'create';

    /**
     * Used for access to dashboard
     * @var string
     */
    public const DASHBOARD = 'dashboard';

    /**
     * Used for access to delete
     * @var string
     */
    public const DELETE = 'delete';

    /**
     * Used for access to display
     * @var string
     */
    public const DISPLAY = 'display';

    /**
     * Used for access to duplicate
     * @var string
     */
    public const DUPLICATE = 'duplicate';

    /**
     * Used for access to help
     * @var string
     */
    public const HELP = 'help';

    /**
     * Used for access to modify
     * @var string
     */
    public const MODIFY = 'modify';

    /**
     * Used for access to slug
     * @var string
     */
    public const SLUG = 'slug';

    /**
     * Contains all the available attributes to check with in supports()
     * @var array
     */
    private const ATTRIBUTES = array(
        self::CREATE,
        self::DASHBOARD,
        self::DELETE,
        self::DISPLAY,
        self::DUPLICATE,
        self::HELP,
        self::MODIFY,
        self::SLUG,
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
            return $subject instanceof GiftVoucherAvailable && in_array($attribute, self::ATTRIBUTES);
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
            case self::CREATE:
            case self::DASHBOARD:
            case self::DELETE:
            case self::DISPLAY:
            case self::DUPLICATE:
            case self::HELP:
            case self::MODIFY:
            case self::SLUG:
                return $this->decisionManager->decide($token, array($this->roleNeeded));
        }

        throw new \LogicException('Invalid attribute: ' . $attribute);
    }
}

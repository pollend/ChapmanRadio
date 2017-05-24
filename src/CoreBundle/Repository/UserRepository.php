<?php
// Copyright 2017, Michael Pollind <polli104@mail.chapman.edu>, All Right Reserved
namespace CoreBundle\Repository;
use CoreBundle\Entity\Role;
use CoreBundle\Entity\User;
use CoreBundle\Entity\UserRole;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository implements UserProviderInterface
{

    public function getUsersByRole($role)
    {
        $users = $this->createQueryBuilder('u')
            ->innerJoin('CoreBundle:Role','co','WITH','co.user_id = u.userid')
            ->groupBy('u.user_id');
        return $users;
    }

    /**
     * @return User
     */
    public  function  create()
    {
        $user = new User();
        return $user;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->findOneByUsernameOrEmail($username);
        if (!$user) {
            throw new UsernameNotFoundException('No user found for username '.$username);
        }
        return $user;
    }

    public  function  findOneByUsernameOrEmail($username)
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :username')
            ->setParameter('username',$username)
            ->getQuery()
            ->getSingleResult();

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf(
                'Instances of "%s" are not supported.',
                $class
            ));
        }

        if (!$refreshedUser = $this->find($user->getId())) {
            throw new UsernameNotFoundException(sprintf('User with id %s not found', json_encode($user->getId())));
        }

        return $refreshedUser;
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }
}

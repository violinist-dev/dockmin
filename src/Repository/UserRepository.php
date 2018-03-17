<?php

namespace App\Repository;

use App\Entity\ServerCredential;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use ParagonIE\Halite\Symmetric\Crypto as SymmetricCrypto;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\HiddenString;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    public function __construct(RegistryInterface $registry, SessionInterface $session, RegistryInterface $doctrine)
    {
        parent::__construct($registry, User::class);
        $this->session = $session;
        $this->doctrine = $doctrine;
    }

    /**
     * Load all server credentials. This usually happens upon login and when new credentials are added to the collection.
     *
     * @param string $password
     *   The userpassword, used for decrypting the credentials.
     * @param User|null $user
     *
     * @throws \ParagonIE\Halite\Alerts\CannotPerformOperation
     * @throws \ParagonIE\Halite\Alerts\InvalidDigestLength
     * @throws \ParagonIE\Halite\Alerts\InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidMessage
     * @throws \ParagonIE\Halite\Alerts\InvalidSalt
     * @throws \ParagonIE\Halite\Alerts\InvalidSignature
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     * @throws \TypeError
     */
    public function loadServerCredentials($password, User $user)
    {
        /** @var ServerCredential[] $server_credentials */
        $server_credentials = $this->getEntityManager()->getRepository(ServerCredential::class)->findBy(['owner' => $user->getId()]);

        $decrypted_credentials = [];
        foreach ($server_credentials as $server_credential) {
            $encryption_salt = $server_credential->getEncryptionSalt();
            $key = KeyFactory::deriveEncryptionKey(new HiddenString($password), $encryption_salt);
            $credentials_decrypted = (array) json_decode(SymmetricCrypto::decrypt($server_credential->getCredentials(), $key));
            $decrypted_credentials[$server_credential->getId()] = $credentials_decrypted;
        }
        $this->session->set('credentials', $decrypted_credentials);
    }

    /**
     * Encrypt all the existing credentials wiht the provided password.
     *
     * @param $password
     *   The userpassword, used for encrypting the credentials.
     * @param User $user
     *
     * @throws \ParagonIE\Halite\Alerts\CannotPerformOperation
     * @throws \ParagonIE\Halite\Alerts\InvalidDigestLength
     * @throws \ParagonIE\Halite\Alerts\InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidMessage
     * @throws \ParagonIE\Halite\Alerts\InvalidSalt
     * @throws \ParagonIE\Halite\Alerts\InvalidSignature
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     * @throws \TypeError
     */
    public function encryptServerCredentials($password, User $user)
    {
        $em = $this->doctrine->getManager();
        $server_credentials_in_session = $this->session->get('credentials');
        $sc_ids = array_keys($server_credentials_in_session);

        foreach ($sc_ids as $id) {
            $server_credential = $this->getEntityManager()->getRepository(ServerCredential::class)->find($id);
            $credentials = $server_credentials_in_session[$id];
            $key = KeyFactory::deriveEncryptionKey(new HiddenString($password), $server_credential->getEncryptionSalt());
            $server_credential->setCredentials(SymmetricCrypto::encrypt(new HiddenString(json_encode($credentials)), $key));
            $em->persist($server_credential);
        }

        $em->flush();
        $this->loadServerCredentials($password, $user);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('s')
            ->where('s.something = :value')->setParameter('value', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}
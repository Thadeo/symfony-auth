<?php

namespace App\Component\Session;

use App\Entity\Sessions;
use App\Entity\User;
use App\Entity\UserActivity;
use App\Entity\UserActivityCategory;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler as BaseHandler;

class PdoSessionHandler extends BaseHandler
{
    private $entityManager;
    private $tokenStorage;
    private $ttl;
    private $expiry;

    public function __construct(\PDO|string $pdoOrDsn, array $options, $entityManager, $tokenStorage)
    {   
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->ttl = $options['ttl'] ?? null;
        $this->expiry = time() + (int) (($this->ttl instanceof \Closure ? ($this->ttl)() : $this->ttl) ?? \ini_get('session.gc_maxlifetime'));
        
        //$entityManager = $entityManager->getConnection()->getNativeConnection();
        parent::__construct($pdoOrDsn, $options);
    }

    /**
     * Write Session
     */
    protected function doWrite($sessionId, $data): bool
    {
        // Find Session
        $session = $this->entityManager->getRepository(Sessions::class)->findOneBy(['ids' => $sessionId]);

        // Verify session if exist
        if($session) {
            
            $session->setUser($this->getUser());
            $session->setData($data);
            $session->setLifetime($this->expiry);
            $session->setTime(time());
            $this->entityManager->flush();

            // Add Activity
            $activity = $this->addUserActivity($this->getUser(), $session, $sessionId);

            // Exception
            if($activity instanceof \Exception) throw new \Exception($activity->getMessage());
            

            return true;
        }
        
        // Prepaire new Data
        $session = new Sessions();
        $session->setDate(new \DateTime());
        $session->setIds($sessionId);
        $session->setUser($this->getUser());
        $session->setIp($_SERVER['REMOTE_ADDR']);
        $session->setBrowser($_SERVER['HTTP_USER_AGENT']);
        $session->setLastLogin(new \DateTime());
        $session->setLifetime($this->expiry);
        $session->setData($data);
        $session->setTime(time());

        // Add Data
        $this->entityManager->persist($session);

        // Flush changes
        $this->entityManager->flush();

        return true;
    }

    /**
     * Current User
     * 
     * get user from token storage
     */
    private function getUser()
    {
        // Verify token
        if($this->tokenStorage->getToken() == null) return null;

        // Return User
        return $this->tokenStorage->getToken()->getUser();
    }

    /**
     * Add User Activity
     * 
     * add activity records
     * 
     * @param User user
     * @param Session session
     * @param string session
     */
    private function addUserActivity(
        User $user = null,
        Sessions $session,
        string $sessionId
    )
    {
        try {
            
            // User not exist
            if($user == null) return false;

            // Find Activity
            $activity = $this->entityManager->getRepository(UserActivity::class)->findOneBy(['session' => $sessionId]);

            // Verify activity if exist
            if($activity) return false;

            // Find Category
            $category = $this->entityManager->getRepository(UserActivityCategory::class)->findOneBy(['code' => 'auth_login']);

            // Prepaire new Data
            $activity = new UserActivity;
            $activity->setDate(new \DateTime());
            $activity->setUser($user);
            $activity->setCategory($category);
            $activity->setIp($session->getIp());
            $activity->setBrowser($session->getBrowser());
            $activity->setSession($sessionId);
            $activity->setMode('test');

            // Add Data
            $this->entityManager->persist($activity);

            // Flush changes
            $this->entityManager->flush();

            // Return true
            return true;
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }
}

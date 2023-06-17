<?php

namespace App\Component\Session;

use App\Entity\Sessions;
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
    /*protected function doWrite($sessionId, $data): bool
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

            return true;
        }
        
        // Prepaire new Data
        $session = new Sessions();
        $session->setDate(new \DateTime());
        $session->setIds($sessionId);
        $session->setUser($this->getUser());
        $session->setLifetime($this->expiry);
        $session->setData($data);
        $session->setTime(time());

        // Add Data
        $this->entityManager->persist($session);

        // Flush changes
        $this->entityManager->flush();

        return true;
    }*/

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
}

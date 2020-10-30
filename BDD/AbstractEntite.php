<?php


namespace transat;


abstract class AbstractEntite
{
    protected $persistant;

    /**
     * @return bool
     */
    public function getPersistant(): bool
    {
        return $this->persistant;
    }

    /**
     * @param bool $persistant
     * @return EntiteSkipper
     */
    public function setPersistant(bool $persistant): AbstractEntite
    {
        $this->persistant = $persistant;
        return $this;
    }
}
<?php
namespace Utils\PaymentVendor\RequestInterface\Traits;

trait MutableDateTimeTrait
{
    private $date_time = null;

    public function getDateTime(): \DateTime
    {
        if (!$this->date_time) {
            return new \DateTime('now', new \DateTimeZone('+0800'));
        }

        return $this->date_time;
    }

    public function setDateTime(\DateTime $date_time): self
    {
        $this->date_time = $date_time;

        return $this;
    }

    public function resetDateTime(): self
    {
        $this->date_time = null;

        return $this;
    }
}
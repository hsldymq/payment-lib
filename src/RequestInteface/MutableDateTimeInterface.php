<?php
namespace Utils\PaymentVendor\RequestInterface;

interface MutableDateTimeInterface
{
    public function getDateTime(): \DateTime;

    public function setDateTime(\DateTime $date_time);

    public function resetDateTime();
}
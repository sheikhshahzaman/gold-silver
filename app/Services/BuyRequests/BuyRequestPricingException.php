<?php

namespace App\Services\BuyRequests;

use RuntimeException;

/** Prices could not be resolved for a buy request. Surfaced to the customer. */
class BuyRequestPricingException extends RuntimeException
{
}

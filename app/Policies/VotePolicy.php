<?php

namespace App\Policies;


/**
 * Class VotePolicy
 * @package App\Policies
 */
class VotePolicy
{
    /**
     * Create a new policy instance.
     * @return void
     */
    public function __construct()
    {
    }


    /**
     * @param Testimonial $testimonial
     * @param User $user
     * @return boolean
     */
    public function isCreatableBy(User $user, Testimonial $testimonial)
    {
        // Some bussiness logic to define whatever user has rights
        // to add new testimonial

        return true;
    }
}

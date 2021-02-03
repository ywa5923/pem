<?php
namespace App\Form\DataTransformer;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\DataTransformerInterface;

use Symfony\Component\Form\Exception\TransformationFailedException;

class FullNameToUserTransformer implements DataTransformerInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var callable
     */
    private $finderCallback;

    public function __construct(UserRepository $userRepository,callable $finderCallback)
    {
        $this->userRepository = $userRepository;
        $this->finderCallback = $finderCallback;
    }

    public function transform($value)
    {

        if(null===$value){
            return '';
        }
        if(!$value instanceof User){
            throw new \LogicException('the user select type can only be used with User Object');
        }

        if(!empty($value->getMiddleName())){
            return sprintf("%s %s %s",$value->getFirstName(),$value->getMiddleName(),$value->getLastName());
        }else{
            return sprintf("%s %s",$value->getFirstName(),$value->getLastName());
        }


    }

    public function reverseTransform($value)
    {
        if(!$value){
            return null;
        }

            $callback = $this->finderCallback;
            $user = $callback($this->userRepository, $value);

            if(!$user){
                throw new TransformationFailedException(sprintf('No user found with full name "%s"',$value));
            }

        return $user;

    }

}
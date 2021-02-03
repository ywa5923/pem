<?php
namespace App\Form;


use App\Form\DataTransformer\FullNameToUserTransformer;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Routing\RouterInterface;

class UserSelectTextType extends AbstractType
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(UserRepository $userRepository, RouterInterface $router)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new FullNameToUserTransformer(
            $this->userRepository,
            $options['finder_callback']
        ));
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message'=>'Hmm....user nor found',
            'finder_callback' => function(UserRepository $userRepository, string $value) {

                $names=explode(' ',$value);
                $searchArray=(count($names)===2)?['firstName'=>$names[0],'lastName'=>$names[1]]:
                    ((count($names)===3)?['firstName'=>$names[0],'middleName'=>$names[1],'lastName'=>$names[2]] :null);

                if($searchArray){

                    return $userRepository->findOneBy($searchArray);
                }else{
                    throw new TransformationFailedException('The user text type can not be transformed to User object');
                }
            },
            'attr'=>[
                'class'=>'js-user-autocomplete',
                'data-autocomplete-url'=>$this->router->generate('get_user_api')
            ]
        ]);
    }


}
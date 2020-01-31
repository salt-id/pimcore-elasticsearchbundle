<?php
/**
 * Created by PhpStorm.
 * User: Yulius Ardian Febrianto <yuliusardin@gmail.com>
 * Date: 31/01/2020
 * Time: 13:11
 */

namespace SaltId\ElasticSearchBundle\Resolver;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ElasticSearchConfigurationResolver
{
    protected $options;

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'general' => function(OptionsResolver $generalResolver) {
                $generalResolver
                    ->setDefined([
                        'hostorip', 'port', 'httpBasicAuthUser', 'httpBasicAuthPassword', 'index',
                    ])
                    ->setDefaults([
                        'hostorip' => '127.0.0.1',
                        'port' => '9200',
                        'index' => 'saltidelasticsearchbundle',
                    ])
                    ->setAllowedTypes('hostorip', ['null', 'string', 'int'])
                    ->setAllowedTypes('port', ['string', 'int'])
                    ->setAllowedTypes('index', ['string']);
            },
        ]);
    }
}
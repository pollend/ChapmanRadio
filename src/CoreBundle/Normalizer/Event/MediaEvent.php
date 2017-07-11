<?php
/**
 * Created by PhpStorm.
 * User: michaelpollind
 * Date: 7/10/17
 * Time: 7:55 PM
 */

namespace CoreBundle\Normalizer\Event;


use CoreBundle\Entity\Media;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\Routing\RouterInterface;

class MediaEvent implements EventSubscriberInterface
{
    private $dispatcher;
    private  $router;

    function __construct(EventDispatcherInterface $dispatcher, RouterInterface $router)
    {
        $this->dispatcher = $dispatcher;
        $this->router = $router;
    }
    /**
     * Returns the events to which this class has subscribed.
     *
     * Return format:
     *     array(
     *         array('event' => 'the-event-name', 'method' => 'onEventName', 'class' => 'some-class', 'format' => 'json'),
     *         array(...),
     *     )
     *
     * The class may be omitted if the class wants to subscribe to events of all classes.
     * Same goes for the format key.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.post_serialize', 'method' => 'onMediaSerialize', 'class' => Media::class)
        );
    }

    public function onMediaSerialize(ObjectEvent $event){
        $mimeTypeGuess = new MimeTypeExtensionGuesser();
        /** @var Media $entity */
        $entity = $event->getObject();
        $event->getVisitor()->addData('uri',$this->router->generate('file_get_media',['source' => $entity->getSource(), 'ext' => $mimeTypeGuess->guess($entity->getMime())],RouterInterface::ABSOLUTE_URL));

    }
}
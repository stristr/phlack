<?php

namespace Crummy\Phlack\Bridge\Symfony\HttpKernel;

use Crummy\Phlack\Bot\Mainframe\Adapter\AbstractAdapter;
use Crummy\Phlack\Bot\Mainframe\Mainframe;
use Crummy\Phlack\Bridge\Symfony\HttpFoundation\RequestConverter;
use Crummy\Phlack\WebHook\Reply\Reply;
use Crummy\Phlack\WebHook\SlashCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class MainframeKernel extends AbstractAdapter implements HttpKernelInterface
{
    /**
     * @param Mainframe                 $mainframe
     * @param callable|RequestConverter $converter
     */
    public function __construct(Mainframe $mainframe = null, callable $converter = null)
    {
        parent::__construct(
            $mainframe ?: new Mainframe(),
            $converter ?: new RequestConverter()
        );
    }

    /**
     * Mediates Request handling between HttpKernelInterface and the Mainframe.
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $converter = $this->converter;
        $command = $converter($request);
        $packet = $this->mainframe->execute($command);
        $content = $packet['output'];

        if ($command instanceof SlashCommand && $content instanceof Reply) {
            $content = $content->get('text');
        }

        return new Response($content);
    }
}

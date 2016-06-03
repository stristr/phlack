<?php

namespace spec\Crummy\Phlack\Bridge\Symfony\Console;

use Crummy\Phlack\Common\Event;
use Crummy\Phlack\Message\Message;
use Crummy\Phlack\WebHook\Converter\StringConverter;
use Crummy\Phlack\WebHook\Mainframe;
use Crummy\Phlack\WebHook\SlashCommand;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleAdapterSpec extends ObjectBehavior
{
    function let(Mainframe $mainframe, StringConverter $converter)
    {
        $this->beConstructedWith($mainframe, $converter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Crummy\Phlack\Bridge\Symfony\Console\ConsoleAdapter');
    }

    function it_executes_the_command_argument($mainframe, InputInterface $input, SlashCommand $cmd, OutputInterface $output, Event $e)
    {
        $this->beConstructedWith($mainframe, function () use ($cmd) {
            return $cmd->getWrappedObject();
        });

        $input->getArgument('command')->shouldBeCalled();
        $mainframe->execute($cmd)->shouldBeCalled()->willReturn($e);
        $reply = new Message('4');
        $e->offsetGet('message')->willReturn($reply);
        $output->writeln($reply['text'])->shouldBeCalled();
        $this->execute($input, $output);
    }
}

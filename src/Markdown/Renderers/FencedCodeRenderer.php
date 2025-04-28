<?php

namespace Guava\FilamentKnowledgeBase\Markdown\Renderers;

use League\CommonMark\Extension\CommonMark\Node\Block\FencedCode;
use League\CommonMark\Extension\CommonMark\Renderer\Block\FencedCodeRenderer as BaseRenderer;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use League\CommonMark\Util\Xml;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

final class FencedCodeRenderer implements NodeRendererInterface
{
    protected BaseRenderer $baseRenderer;

    protected Shiki $shiki;

    public function __construct()
    {
        $this->shiki = new Shiki;
        $this->baseRenderer = new BaseRenderer;
    }

    private function getLanguage($language): string
    {
        $home = getenv('HOME');
        $command = [
            (new ExecutableFinder)->find('node', 'node', [
                '/usr/local/bin',
                '/opt/homebrew/bin',
                $home . '/n/bin', // support https://github.com/tj/n
            ]),
            'grammars.js',
            $language,
        ];

        $path = realpath(__DIR__ . '/../../../bin');

        $process = new Process(
            $command,
            $path,
            null,
        );

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    public function render(
        Node $node,
        ChildNodeRendererInterface $childRenderer
    ): string {
        /** @var HtmlElement $element */
        $element = $this->baseRenderer->render($node, $childRenderer);

        $languageId = $this->getSpecifiedLanguage($node) ?? 'text';
        $languageName = $this->getLanguage($languageId);

        $pattern = '/<code[^>]*>(.*)<\/code>/is';
        $replacement = '$1';

        $result = preg_replace($pattern, $replacement, $element->getContents());
        $result = htmlspecialchars_decode($result);
        $code = $this->shiki->highlightCode(
            $result,
            $languageId,
            'github-dark',
        );
        $element->setContents(
            $code
        );

        return view('filament-knowledge-base::code-block', [
            'code' => $element->getContents(),
            'language' => $languageName,
        ]);
    }

    protected function getSpecifiedLanguage(FencedCode $block): ?string
    {
        $infoWords = $block->getInfoWords();

        if (empty($infoWords) || empty($infoWords[0])) {
            return null;
        }

        return Xml::escape($infoWords[0]);
    }
}

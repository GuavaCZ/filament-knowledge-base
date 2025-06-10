<?php

namespace Guava\FilamentKnowledgeBase\Concerns;

use Closure;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\EnvironmentBuilderInterface;

trait CanConfigureCommonMark
{
    protected ?Closure $configureCommonMarkEnvironmentUsing = null;

    protected ?Closure $configureCommonMarkOptionsUsing = null;

    protected ?Closure $configureCommonMarkConverterUsing = null;

    public function configureCommonMarkEnvironmentUsing(Closure $using): static
    {
        $this->configureCommonMarkEnvironmentUsing = $using;

        return $this;
    }

    public function configureCommonMarkEnvironment(EnvironmentBuilderInterface $environment): EnvironmentBuilderInterface
    {
        return $this->evaluate($this->configureCommonMarkEnvironmentUsing, [
            'environment' => $environment,
        ]) ?? $environment;
    }

    public function configureCommonMarkOptionsUsing(Closure $using): static
    {
        $this->configureCommonMarkOptionsUsing = $using;

        return $this;
    }

    public function configureCommonMarkOptions(array $options): array
    {
        return $this->evaluate($this->configureCommonMarkOptionsUsing, [
            'options' => $options,
        ]) ?? $options;
    }

    public function configureCommonMarkConverterUsing(Closure $using): static
    {
        $this->configureCommonMarkConverterUsing = $using;

        return $this;
    }

    public function configureCommonMarkConverter(ConverterInterface $converter): ConverterInterface
    {
        return $this->evaluate($this->configureCommonMarkConverterUsing, [
            'converter' => $converter,
        ]) ?? $converter;
    }
}

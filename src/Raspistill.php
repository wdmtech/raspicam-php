<?php

namespace Cvuorinen\Raspicam;

use AdamBrett\ShellWrapper\Command\CommandInterface;

/**
 * Class that abstracts the usage of raspistill cli utility that is used to take photos with the
 * Raspberry Pi camera module.
 *
 * @package Cvuorinen\Raspicam
 */
class Raspistill extends Raspicam
{
    const COMMAND = 'raspistill';

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var bool
     */
    protected $verticalFlip;

    /**
     * @var bool
     */
    protected $horizontalFlip;

    /**
     * @var int
     */
    protected $sharpness;

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function verticalFlip($value = true)
    {
        $this->verticalFlip = (bool) $value;

        return $this;
    }

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function horizontalFlip($value = true)
    {
        $this->horizontalFlip = (bool) $value;

        return $this;
    }

    /**
     * @param int $value
     *
     * @return $this
     */
    public function sharpness($value)
    {
        $this->assertIntBetween($value, -100, 100);

        $this->sharpness = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getExecutable()
    {
        return self::COMMAND;
    }

    /**
     * @param string $filename
     */
    public function takePicture($filename)
    {
        if (empty($filename)) {
            throw new \InvalidArgumentException('Filename required');
        }

        $this->filename = $filename;

        $this->execute(
            $this->buildCommand()
        );
    }

    /**
     * @return CommandInterface
     */
    private function buildCommand()
    {
        $command = $this->getCommandBuilder();

        if ($this->filename) {
            $command->addArgument('output', $this->filename);
        }

        if ($this->verticalFlip) {
            $command->addArgument('vflip');
        }

        if ($this->horizontalFlip) {
            $command->addArgument('hflip');
        }

        if (null !== $this->sharpness) {
            $command->addArgument('sharpness', $this->sharpness);
        }

        return $command;
    }

    /**
     * @param int $value
     * @param int $min
     * @param int $max
     */
    private function assertIntBetween($value, $min, $max)
    {
        if (!is_int($value) || $value < $min || $value > $max) {
            throw new \InvalidArgumentException(
                sprintf('Expected integer between %s and %s', $min, $max)
            );
        }
    }
}

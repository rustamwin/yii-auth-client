<?php

declare(strict_types=1);

namespace Yiisoft\Yii\AuthClient;

/**
 * ClientInterface declares basic interface all Auth clients should follow.
 */
interface ClientInterface
{
    /**
     * @return string service name.
     */
    public function getName(): string;

    /**
     * @return string service title.
     */
    public function getTitle(): string;

    /**
     * @return array list of user attributes
     */
    public function getUserAttributes(): array;

    /**
     * @return array view options in format: optionName => optionValue
     */
    public function getViewOptions(): array;
}

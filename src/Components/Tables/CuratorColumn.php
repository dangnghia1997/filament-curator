<?php

namespace Awcodes\Curator\Components\Tables;

use Awcodes\Curator\Facades\Curator;
use Awcodes\Curator\Models\Media;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CuratorColumn extends ImageColumn
{
    protected string $view = 'curator::components.tables.curator-column';

    protected int|null $limit = null;

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @deprecated use app('curator')->isResizable($ext) instead
     */
    public function isImage(): bool
    {
        $state = $this->getState();

        if (filled($state)) {
            if (is_a($state, Media::class)) {
                $url = $state->path;
            } else {
                $url = $state;
            }

            $ext = Str::of($url)->afterLast('.');

            return Curator::isResizable($ext);
        }

        return false;
    }

    public function getMedia(): Media|Collection|array|null
    {
        $record = $this->getRecord();

        if (! is_a($record, Media::class)) {
            $state = $this->getState();

            if (is_a($state, Collection::class)) {
                return $state->take($this->limit);
            }

            if (is_a($state, Media::class)) {
                return Arr::wrap($state);
            }

            $state = Arr::wrap($state);
            return app('curator')->getMedia(array_slice($state, 0, $this->limit));
        }

        return Arr::wrap($record);
    }
}

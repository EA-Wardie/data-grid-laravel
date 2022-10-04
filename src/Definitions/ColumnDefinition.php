<?php

namespace Eawardie\DataGrid\Definitions;

use Closure;
use Eawardie\DataGrid\Constants\DataGridConstants;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use Throwable;

class ColumnDefinition implements Arrayable
{
    //all column properties
    private string $timestampFormat = 'D MMMM YYYY';

    private string $type = 'text';

    private string $label = '';

    private ?string $avatar = null;

    private ?string $value = null;

    private ?string $rawValue = null;

    private ?string $subtitle = null;

    private ?string $rawSubtitle = null;

    private ?string $subtitleType = null;

    private ?string $iconConditionValue = null;

    private array $enumerators = [];

    private array $iconMap = [];

    private bool $hidden = false;

    private bool $searchable = true;

    private bool $sortable = true;

    private bool $isRaw = false;

    private bool $isAggregate = false;

    private bool $avatarPreview = true;

    //all column types
    private const COLUMN_TYPES = ['text', 'email', 'number', 'perc', 'timestamp', 'enum', 'icon'];

    //all column types that are accepted as advanced
    private const ADVANCED_COLUMN_TYPES = ['number', 'perc', 'timestamp', 'enum', 'icon'];

    public function __construct()
    {
    }

    /**
     * @throws Throwable
     */
    //function to specify an avtar
    //takes a file id identifier and whether the avatar can be views in a larger menu on the front-end
    public function avatar(string $identifier = 'profilepic_fileid', bool $preview = true): ColumnDefinition
    {
        $this->avatar = $identifier;
        $this->avatarPreview = $preview;
        $this->validateAvatar();

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to specify an icon column
    //can be a simple icon or takes a IconDefinition closure for condition based icons per items
    public function icon(string $iconConditionValue, $icon, string $color = 'grey'): ColumnDefinition
    {
        $this->iconConditionValue = $iconConditionValue ?? null;

        if (gettype($icon) === 'string') {
            $this->iconMap = (new IconDefinition())
                ->default($icon, $color)
                ->toArray();
        } elseif ($icon instanceof Closure) {
            $this->iconMap = $icon(new IconDefinition())->toArray();
        }

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to specify the value identifier of the column
    public function value(string $value): ColumnDefinition
    {
        if (str_contains($value, '.')) {
            $valueArray = explode('.', $value);
            $this->value = $valueArray[count($valueArray) - 1];
        } else {
            $this->value = $value;
        }

        if (! $this->rawValue) {
            $this->rawValue = $value;
        }

        $this->isRaw = false;
        $this->isAggregate = false;
        $this->validateValue();

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to specify the taw value identifier of the column
    public function rawValue(string $rawValue): ColumnDefinition
    {
        $this->rawValue = $rawValue;
        $this->isRaw = true;
        $this->isAggregate = Str::contains(strtoupper($rawValue), DataGridConstants::AGGREGATES);
        $this->validateRawValue();

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to specify the subtitle value identifier of the column
    public function subtitle(string $subtitle): ColumnDefinition
    {
        if (str_contains($subtitle, '.')) {
            $valueArray = explode('.', $subtitle);
            $this->subtitle = $valueArray[count($valueArray) - 1];
        } else {
            $this->subtitle = $subtitle;
        }

        if (! $this->rawSubtitle) {
            $this->rawSubtitle = $subtitle;
        }

        $this->validateSubtitle();

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to specify the subtitle raw value identifier of the column
    public function rawSubtitle($rawValue): ColumnDefinition
    {
        $this->rawSubtitle = $rawValue;
        $this->validateRawSubtitle();

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to specify the column type
    //must be one of the previously addressed column types
    public function type(string $type): ColumnDefinition
    {
        $this->type = $type;
        $this->validateType();

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to specify the subtitle column type
    public function subtitleType(string $type): ColumnDefinition
    {
        $this->subtitleType = $type;
        $this->validateSubtitleType();

        return $this;
    }

    //function to specify the column label
    public function label(string $label): ColumnDefinition
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @throws Throwable
     */
    //function to specify the column enumerator values
    //must a key value pair
    public function enumerators($items): ColumnDefinition
    {
        if (gettype($items) === 'array') {
            $this->enumerators = $items;
        } elseif ($items instanceof Closure) {
            $this->enumerators = $items((new EnumDefinition())->toArray());
        }

        $this->validateEnumerators();

        return $this;
    }

    //function to specify if the column is hidden by default
    public function hidden(bool $hidden = false): ColumnDefinition
    {
        $this->hidden = $hidden;

        return $this;
    }

    //function to specify whether the column can be used in search queries
    public function searchable(bool $searchable = true): ColumnDefinition
    {
        $this->searchable = $searchable;

        return $this;
    }

    //function to whether the column can be used in sorting
    public function sortable(bool $sortable = true): ColumnDefinition
    {
        $this->sortable = $sortable;

        return $this;
    }

    //function to set the timestamp format for that column
    //used when the column contains a timestamp value
    public function timestampFormat(string $format = 'D MMMM YYYY')
    {
        $this->timestampFormat = $format;
    }

    /**
     * @throws Throwable
     */
    //returns the complete column with all its properties
    public function toArray(): array
    {
        $this->validateLabel();
        $this->validateEnumeratorItems();

        return [
            'avatar' => $this->avatar,
            'avatarPreview' => $this->avatarPreview,
            'iconMap' => $this->iconMap,
            'value' => $this->value,
            'enumerators' => $this->enumerators,
            'isRaw' => $this->isRaw,
            'isAggregate' => $this->isAggregate,
            'rawValue' => $this->rawValue,
            'subtitle' => $this->subtitle,
            'rawSubtitle' => $this->rawSubtitle,
            'type' => $this->type,
            'subtitleType' => $this->subtitleType,
            'label' => $this->label,
            'hidden' => $this->hidden,
            'searchable' => $this->searchable,
            'sortable' => $this->sortable,
            'timestampFormat' => $this->timestampFormat,
            'isAdvanced' => in_array($this->type, self::ADVANCED_COLUMN_TYPES),
            'iconConditionValue' => explode('.', $this->iconConditionValue)[1] ?? null,
            'iconConditionRawValue' => $this->iconConditionValue ?? null,
        ];
    }

    /**
     * @throws Throwable
     */
    //validates the column icon map
    private function validateAvatar()
    {
        throw_if(count($this->iconMap) > 0, new Exception("Column with an 'avatar' cannot have an 'icon'."));
    }

    /**
     * @throws Throwable
     */
    //validates the column value to ensure it does not contain advanced sql properties
    private function validateValue()
    {
        throw_if(str_contains(strtolower($this->value), ' as '), new Exception("Value cannot contain sql identifiers like 'AS'. Use rawValue() instead."));
    }

    /**
     * @throws Throwable
     */
    //validates the column's raw value
    private function validateRawValue()
    {
        throw_if(! $this->value, new Exception('Value alias is required when using rawValue(), add it using value().'));
        throw_if(str_contains(strtolower($this->rawValue), ' as '), new Exception("Raw value cannot contain identifiers like 'AS'. Pass an alias with value()."));
    }

    /**
     * @throws Throwable
     */
    //validates the column subtitles
    //very similar to validating the column value
    private function validateSubtitle()
    {
        throw_if(str_contains(strtolower($this->subtitle), ' as '), new Exception("Subtitle cannot contain sql identifiers like 'AS'. Use rawSubtitle() instead."));
    }

    /**
     * @throws Throwable
     */
    //validates the column raw subtitle value
    //very similar to validating the column raw value
    private function validateRawSubtitle()
    {
        throw_if(! $this->subtitle, new Exception('Subtitle alias is required when using rawSubtitle(), add it using subtitle().'));
        throw_if(str_contains(strtolower($this->rawSubtitle), ' as '), new Exception("Raw subtitle cannot contain identifiers like 'AS'. Pass an alias with subtitle()."));
    }

    /**
     * @throws Throwable
     */
    //validates the column type
    //ensures specified column type is an allowed type
    private function validateType()
    {
        throw_if($this->type === 'timestamp', "Timestamp is an invalid column type. Did you mean 'datetime'?");
        throw_if(! in_array($this->type, self::COLUMN_TYPES), new Exception("Column type '".$this->type."' is not an allowed column type."));
    }

    /**
     * @throws Throwable
     */
    //validates the column type
    //ensures specified column type is an allowed type
    private function validateLabel()
    {
        throw_if(! $this->label, 'A label is required for column "'.$this->value.'".');
    }

    /**
     * @throws Throwable
     */
    //validates the column subtitle type
    //very similar to validating the column type
    private function validateSubtitleType()
    {
        $wrongTimestamp = $this->subtitleType === 'time' || $this->subtitleType === 'date' || $this->subtitleType === 'datetime';
        throw_if($wrongTimestamp, $this->subtitleType." is an invalid column type. Did you mean 'timestamp'?");
        throw_if(! in_array($this->subtitleType, self::COLUMN_TYPES), new Exception("Subtitle column type '".$this->subtitleType."' is not an allowed column type."));
    }

    /**
     * @throws Throwable
     */
    //validates the column enumerators
    private function validateEnumerators()
    {
        throw_if(count($this->enumerators) === 0, 'Enumerators cannot be empty.');
    }

    /**
     * @throws Throwable
     */
    //validates the enumerator items to ensure they consist of key value pairs
    private function validateEnumeratorItems()
    {
        throw_if($this->type === 'enum' && count($this->enumerators) === 0, "When using column type 'enum', enumerator items are required. Add at least one using enumerators().");
        throw_if(count($this->enumerators) > 0 && $this->type !== 'enum', "Enumerator items specified with not 'enum' column type.");
    }
}

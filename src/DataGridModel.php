<?php

namespace Eawardie\DataGrid;

use App\Models\User\User;
use Eloquent;
use IanRothmann\Database\Eloquent\ModelConvention;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\DataGrid\DataGrid
 *
 * @property int $id
 * @property int|null $ownerid
 * @property string|null $table
 * @property mixed|null $configuration
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $owner
 *
 * @method static Builder|DataGrid joinRelation($relationship_name, $join = '')
 * @method static Builder|DataGrid newModelQuery()
 * @method static Builder|DataGrid newQuery()
 * @method static Builder|DataGrid query()
 * @method static Builder|DataGrid whereConfiguration($value)
 * @method static Builder|DataGrid whereCreatedAt($value)
 * @method static Builder|DataGrid whereDatadisplaysystemid($value)
 * @method static Builder|DataGrid whereOwnerid($value)
 * @method static Builder|DataGrid whereTable($value)
 * @method static Builder|DataGrid whereUpdatedAt($value)
 * @method static Builder|DataGrid whereUrl($value)
 * @mixin Eloquent
 */
class DataGridModel extends Model
{
    use ModelConvention;

    protected $guarded = [];

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'ownerid', 'userid');
    }

    public static function getConfiguration(string $tableRef)
    {
        return self::where('ownerid', auth()->id())
            ->where('table', $tableRef)
            ->first();
    }

    public static function authHasConfiguration(string $tableRef): bool
    {
        return (bool) self::getConfiguration($tableRef);
    }

    public static function getConfigurationData(string $tableRef): array
    {
        if (auth() && $tableRef) {
            return collect(json_decode(self::getConfiguration($tableRef)->configuration, true))
                ->toArray();
        }

        return [];
    }

    public static function setConfigurationData(string $tableRef, array $data): Collection
    {
        $config = self::getConfiguration($tableRef);

        if ($config) {
            $config->update([
                'configuration' => json_encode($data),
            ]);
        } else {
            /** @var User $user */
            $user = auth()->user();
            $config = $user->dataDisplaySystems()
                ->create([
                    'table' => $tableRef,
                    'configuration' => json_encode($data),
                ]);
        }

        return collect(json_decode($config->configuration, true));
    }

    public static function updateConfigurationValue(string $tableRef, string $key, $value)
    {
        /** @var DataGrid $config */
        $data = self::getConfigurationData($tableRef);
        $data[$key] = $value;
        self::setConfigurationData($tableRef, $data);
    }
}

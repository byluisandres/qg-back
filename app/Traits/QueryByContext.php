<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait QueryByContext
{
    protected static $ctxQuery;
    protected static $ctxParams;

    public static function fromContext()
    {
        self::getContextParams();

        self::contextSelect();
        self::contextSearch();
        self::contextFilters();
        self::contextOrder();
        self::contextPagination();
        self::contextWithCount();
        self::contextScopes();
        $data = self::$ctxQuery->get();
        $itemCount = self::itemCount();

        // load relations
        self::contextWith($data);

        return ["data" => $data, 'itemCount' => $itemCount];
    }

    /**
     * Obtiene todos los parámetros de la request
     *
     * @return void
     */
    public static function getContextParams()
    {
        self::$ctxParams = request()->all();
    }

    /**
     * Select con los campos
     *
     * @return void
     */
    public static function contextSelect()
    {

        if (isset(self::$ctxParams['fields'])) {
            $filter = self::$ctxParams['fields'];
            if (is_array(json_decode($filter))) {
                $columnList = json_decode($filter);
            } else {
                $columnList = explode(",", $filter);
            }

            $result = array_map(function ($item) {
                return trim($item);
            }, $columnList);

            self::$ctxQuery = static::select(DB::raw('SQL_CALC_FOUND_ROWS '.array_shift($result)))
                ->addSelect($result);
        } else {
            self::$ctxQuery = static::select(DB::raw("SQL_CALC_FOUND_ROWS *"));
        }
    }

    /**
     * Busqueda
     *
     * @return void
     */
    public static function contextSearch()
    {
        if (isset(self::$ctxParams['filter']) || isset(self::$ctxParams['search'])) {
            $search = self::$ctxParams['filter'] ?? self::$ctxParams['search'];
            self::$ctxQuery->where(function ($query) use ($search) {
                $searchColumns = self::$searchColumns;
                foreach ($searchColumns as $value) {
                    $query->orWhere($value, 'like', "%$search%");
                }
            });
        }
    }

    /**
     * Ordenado por
     *
     * @return void
     */
    public static function contextOrder()
    {
        if (isset(self::$ctxParams['sortBy'])) {
            $order = self::$ctxParams['sortBy'];
            if (isset(self::$ctxParams['sortDesc'])) {
                $sortDesc = self::ctxParamToBoolean(self::$ctxParams['sortDesc']);
            } else {
                $sortDesc = false;
            }
            self::$ctxQuery->orderBy($order, $sortDesc ? 'DESC': 'ASC');
        }
    }

    /**
     * Paginado
     *
     * @return void
     */
    public static function contextPagination()
    {
        if (isset(self::$ctxParams['perPage'])) {
            $perPage = intval(self::$ctxParams['perPage']);
            if (isset(self::$ctxParams['page'])) {
                $page = intval(self::$ctxParams['page']);
            } else {
                $page = 1;
            }
            self::$ctxQuery->skip($perPage*($page-1))->take($perPage);
        }
    }

    /**
     * Otros filtros
     *
     * @return void
     */
    public static function contextFilters()
    {
        if (isset(self::$ctxParams['filters'])) {
            $filters = json_decode(self::$ctxParams['filters'], true);
            foreach ($filters as $column => $value) {
                self::applyFilters($column, $value);
            }
        }
    }

    public static function applyFilters($column, $value)
    {
        if (is_scalar($value)) {
            self::$ctxQuery->where($column, $value);
        } else {
            foreach ($value as $i => $y) {
                switch ($i) {
                    case 'gt':
                        self::$ctxQuery->where($column, ">", $y);
                        break;
                    case 'get':
                        self::$ctxQuery->where($column, ">=", $y);
                        break;
                    case 'le':
                        self::$ctxQuery->where($column, "<", $y);
                        break;
                    case 'let':
                        self::$ctxQuery->where($column, "<=", $y);
                        break;
                    case 'eq':
                        self::$ctxQuery->where($column, "=", $y);
                        break;
                    case 'in':
                        self::$ctxQuery->whereIn($column, $y);
                        break;
                    case 'isNull':
                        if (self::ctxParamToBoolean($y)) {
                            self::$ctxQuery->whereNull($column);
                        } else {
                            self::$ctxQuery->whereNotNull($column);
                        }
                        break;
                    default:
                        break;
                }
            }
        }

    }

    public static function ctxParamToBoolean($y) {
        return strtolower($y) === 'true';
    }

    /**
     * Obtiene el número de items en la relación
     */
    public static function contextWithCount()
    {
        if (isset(self::$ctxParams['withCount'])) {
            self::$ctxQuery->withCount(self::$ctxParams['withCount']);
        }
    }

    /**
     * Relaciones
     *
     * @param $data
     * @return void
     */
    public static function contextWith($data)
    {
        if (isset(self::$ctxParams['with'])) {
            /**
             * Ver si hay : entonces hacer el explode por el "|"
             * sino explode por el ","
             */
            if (str_contains(self::$ctxParams['with'], ":")) {
                $relations = explode("|", self::$ctxParams['with']);
            } else {
                $relations = explode(",", self::$ctxParams['with']);
            }
            $data->load($relations);
        }
    }

    /**
     * Filtrado de los scopes
     *
     */

    public static function contextScopes()
    {
        if (isset(self::$ctxParams['scopes'])) {
            $scopes = json_decode(self::$ctxParams['scopes'], true);
            foreach ($scopes as $key => $value) {
                if (is_array($value)) {
                    $filter = $value;
                } else {
                    $filter = explode(",", $value);
                }
                if (method_exists(self::class, self::ctxNameScope($key))) {
                    self::scopeModule(self::$ctxQuery, $filter);
                }
            }
        }
    }

    /**
     * Devuelve el nombre del scope
     *
     * @param String $key
     * @return void
     */
    public static function ctxNameScope($key)
    {
        return 'scope' . ucfirst($key);
    }

    /**
     * Número de items en base a los filtros
     *
     * @return void
     */
    public static function itemCount()
    {
        return  DB::select(DB::raw("SELECT FOUND_ROWS() AS 'total'"))[0]->total;
    }
}

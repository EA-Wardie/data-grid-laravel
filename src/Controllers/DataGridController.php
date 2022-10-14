<?php

namespace Eawardie\DataGrid\Controllers;

use App\Http\Controllers\Controller;
use Eawardie\DataGrid\Models\DataGrid;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DataGridController extends Controller
{
    //function to set filter from front-end
    //can be used with data config or session
    public function filters($ref, Request $request): RedirectResponse
    {
        $filters = $request->get('filters', []);

        $session = session($ref);
        $session['filters'] = $filters;
        session()->put($ref, $session);

        DataGrid::updateConfigurationValue($ref, 'filters', $filters);

        return redirect()->back();
    }

    //function to set search from front-end when using search in a session context
    public function search($ref, Request $request): RedirectResponse
    {
        $search = $request->get('search', []);
        $session = session($ref);
        $session['search'] = $search;

        session()->put($ref, $session);

        return redirect()->back();
    }

    //function to set sort from front-end when using sort in a session context
    public function sort($ref, Request $request): RedirectResponse
    {
        $sortBy = $request->get('sortBy', []);
        $session = session($ref);
        $session['sortBy'] = $sortBy;
        session()->put($ref, $session);

        return redirect()->back();
    }

    //function to set page from front-end when using page in a session context
    public function page($ref, Request $request): RedirectResponse
    {
        $page = $request->get('page', 1);
        $session = session($ref);
        $session['page'] = $page;
        session()->put($ref, $session);

        return redirect()->back();
    }

    //function to set the selected layout from the front-end
    public function layout($ref, Request $request): RedirectResponse
    {
        $layoutId = $request->get('layout');
        $sortBy = $request->get('sort', []);
        DataGrid::updateConfigurationValue($ref, 'currentLayout', $layoutId);

        $config = DataGrid::getConfigurationData($ref);
        $layouts = $config['layouts'];

        if (count($layouts) > 0) {
            $layouts = collect($layouts)->map(function ($layout) use ($layoutId, $sortBy) {
                $layout['current'] = $layout['id'] === $layoutId;
                $layout['custom'] = true;
                $layout['sort'] = $sortBy;

                return $layout;
            })->toArray();
            DataGrid::updateConfigurationValue($ref, 'layouts', $layouts);
        }

        $session = session($ref);
        $session['filters'] = [];
        session()->put($ref, $session);
        DataGrid::updateConfigurationValue($ref, 'filters', []);

        return redirect()->back();
    }

    //function sued to add new custom layouts
    public function add($ref, Request $request): RedirectResponse
    {
        $layout = $request->get('layout');
        $layout['custom'] = true;
        $layout['current'] = true;
        $config = DataGrid::getConfigurationData($ref);
        $layouts = collect($config['layouts'])->map(function ($layout) {
            $layout['current'] = false;
            return $layout;
        })->toArray();
        $layouts[] = $layout;
        DataGrid::updateConfigurationValue($ref, 'layouts', $layouts);
        DataGrid::updateConfigurationValue($ref, 'currentLayout', $layout['id']);

        return redirect()->back();
    }

    public function remove($ref, Request $request): RedirectResponse
    {
        $layout = $request->get('layout');
        $config = DataGrid::getConfigurationData($ref);
        $currentId = $config['currentLayout'];
        $layouts = collect($config['layouts'])->reject(function ($value) use ($layout, $currentId, $ref) {
            if ($currentId === $value['id']) {
                DataGrid::updateConfigurationValue($ref, 'currentLayout', null);
            }

            return $value['id'] === $layout['id'];
        });

        DataGrid::updateConfigurationValue($ref, 'layouts', $layouts);

        return redirect()->back();
    }

    //function used to create a custom layout and apply it from the front-end
    public function view($ref, Request $request): RedirectResponse
    {
        $columns = collect($request->get('columns', []));
        $layoutColumns = $columns->map(function ($column) {
            $value = $column['isRaw'] ? $column['value'] : $column['rawValue'];
            return [
                'value' => $value,
                'order' => $column['index'],
                'hidden' => $column['hidden'],
            ];
        });
        $id = 'custom_hidden';
        $layout = [
            'id' => $id,
            'columns' => $layoutColumns->toArray(),
            'label' => null,
            'current' => false,
            'custom' => true,
        ];

        if ($columns->count() > 0) {
            DataGrid::updateConfigurationValue($ref, 'currentLayout', $id);
            DataGrid::updateConfigurationValue($ref, 'layouts', [$layout]);
        }

        return redirect()->back();
    }
}

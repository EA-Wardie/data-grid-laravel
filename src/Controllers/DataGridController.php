<?php

namespace Eawardie\DataGrid\Controllers;

use App\Http\Controllers\Controller;
use Eawardie\DataGrid\Models\DataGridModel;
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

        DataGridModel::updateConfigurationValue($ref, 'filters', $filters);

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
        DataGridModel::updateConfigurationValue($ref, 'currentLayout', $layoutId);

        $config = DataGridModel::getConfigurationData($ref);
        $layouts = $config['layouts'];

        if (count($layouts) > 0) {
            $layouts[0]['current'] = $layouts[0]['id'] === $layoutId;
            DataGridModel::updateConfigurationValue($ref, 'layouts', $layouts);
        }

        $session = session($ref);
        $session['filters'] = [];
        session()->put($ref, $session);
        DataGridModel::updateConfigurationValue($ref, 'filters', []);

        return redirect()->back();
    }

    //function used to create a custom layout and apply it from the front-end
    public function view($ref, Request $request): RedirectResponse
    {
        $columns = $request->get('columns', []);
        $visibleColumns = collect($columns)->where('hidden', false);
        $layoutColumns = $visibleColumns->map(function ($column) {
            $value = $column['isRaw'] ? $column['value'] : $column['rawValue'];
            return [
                'value' => $value,
                'order' => $column['index'],
                'hidden' => false,
            ];
        });
        $id = 'custom_1';
        $layout = [
            'id' => $id,
            'columns' => $layoutColumns->toArray(),
            'label' => 'Custom View',
            'current' => true,
        ];

        if ($visibleColumns->count() > 0) {
            DataGridModel::updateConfigurationValue($ref, 'currentLayout', $id);
            DataGridModel::updateConfigurationValue($ref, 'layouts', [$layout]);
        }

        return redirect()->back();
    }
}

<?php

namespace HeadlessLaravel\Formations\Http\Controllers;

class ResourceController extends Controller
{
    public function index()
    {
        $this->check('viewAny', $this->model());

        $this->formation()->validate();

        return $this->response(
            'index',
            $this->formation()->results()
        );
    }

    public function create()
    {
        $this->check('create', $this->model());

        return $this->response('create');
    }

    public function store()
    {
        $this->check('create', $this->model());

        $resource = $this->formation()->create($this->model(), $this->values());

        $this->formation()->created($resource);

        return $this->response('store', $resource);
    }

    public function show()
    {
        $resource = $this->resource();

        $this->check('view', $resource);

        return $this->response('show', $resource);
    }

    public function edit()
    {
        $resource = $this->resource();

        $this->check('update', $resource);

        return $this->response('edit', $resource);
    }

    public function update()
    {
        $resource = $this->resource();

        $this->check('update', $resource);

        $resource = $this->formation()->update($resource, $this->values());

        $this->formation()->updated($resource);

        return $this->response('update', $resource);
    }

    public function destroy()
    {
        $resource = $this->resource();

        $this->check('delete', $resource);

        $this->formation()->delete($resource);

        return $this->response('destroy', $resource);
    }

    public function restore()
    {
        $resource = $this->resource();

        $this->check('restore', $resource);

        $this->formation()->restore($resource);

        return $this->response('restore', $resource);
    }

    public function forceDelete()
    {
        $resource = $this->resource();

        $this->check('forceDelete', $resource);

        $this->formation()->forceDelete($resource);

        return $this->response('force-delete', $resource);
    }
}

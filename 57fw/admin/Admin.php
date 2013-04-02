<?php
namespace Admin;

class Admin extends \Core\Component {
    protected $e;
    protected $models = array();
    protected $config = array(
        'url_prefix' => '/admin/',
        'validators' => array(
            '\Uac\Validators::super'
        )
    );

    public function register($instance) {
        if ($instance instanceof \Core\ComponentDispatcher) {
            if (!isset($this->models[$instance->getName()]))
                $this->models[$instance->getName()] = array();

            foreach ($instance->getModels() as $v) {
                $this->models[$instance->getName()][] = $v;
            }
        }
    }

    public function engage($e) {
        $this->e = $e;
        $e->twig->addFunction(new \Twig_SimpleFunction('mkmodel', function ($x) {
            return str_replace('\\', '.', $x);
        }));

        $e->router->register('dash/', array($this, 'dash'), $this);
        $e->router->register('model/([\w\.]+)/', array($this, 'show'), $this);
        $e->router->register('model/([\w\.]+)/(\d+)/', array($this, 'edit'), $this);
        $e->router->register('model/([\w\.]+)/new/', array($this, 'create'), $this);
        $e->router->register(
            'model/([\w\.]+)/(\d+)/delete/', 
            array($this, 'delete'),
            $this
        );
    }

    public function delete($req, $enc_model, $pk) {
        $model = str_replace('.', '\\', $enc_model);
        $component = explode('\\', $model);
        array_shift($component);
        $component = array_shift($component);
        $man = $this->e->man($model);

        $instance = $man->get($pk);

        $man->delete($instance);

        return new \Http\RedirectResponse(
            $this->e->router->make('admin.show', $enc_model)
        );
    }

    public function create($req, $enc_model) {
        $model = str_replace('.', '\\', $enc_model);
        $component = explode('\\', $model);
        array_shift($component);
        $component = array_shift($component);
        $man = $this->e->man($model);

        $form = (new \Form\Form($this->e, 
            $req->post()
        ))->setModel($model);
        $form->getField('__submit')->addClass('-btn -primary-');

        if ($form->isValid() && ($data = $form->getData())) {
            $instance = $man->buildInstance($data);
            $man->save($instance);
            return new \Http\RedirectResponse($this->e->router->make(
                'admin.edit', $enc_model, $instance->getPKey()->getValue()
            ));
        }

        return $this->e->twig->render('admin/edit.html', array(
            'form' => $form,
            'component' => $component,
            'model' => $model
        ));
    }

    public function edit($req, $enc_model, $pk, $remove=false) {
        $model = str_replace('.', '\\', $enc_model);
        $component = explode('\\', $model);
        array_shift($component);
        $component = array_shift($component);
        $man = $this->e->man($model);

        $instance = $man->get($pk);

        $form = (new \Form\Form($this->e, 
            $req->post() ? $req->post() : $instance
        ))->setModel($model);
        $form->getField('__submit')->addClass('-btn -primary-');
        if ($form->isValid() && ($data = $form->getData())) {
            foreach ($data as $k => $v) {
                if (0 !== strpos($k, '__')) {
                    $instance->getField($k)->setValue($v);
                }
            }
            $man->save($instance);
        }

        return $this->e->twig->render('admin/edit.html', array(
            'form' => $form,
            'component' => $component,
            'model' => $model
        ));
    }

    public function show($req, $model) {
        $model = str_replace('.', '\\', $model);
        $component = explode('\\', $model);
        array_shift($component);
        $component = array_shift($component);
        $man = $this->e->man($model);
        $fields = array_map(function ($field) {
            return $field->getName();
        }, $man->getModelInstance()->getFields());

        return $this->e->twig->render('admin/show.html', array(
            'fields' => $fields,
            'instances' => $man->find(),
            'component' => $component,
            'model' => $model
        ));
    }

    public function dash() {
        $models = array();
        foreach ($this->models as $name => $arr) {
            $app = array(
                'name' => $name,
                'models' => array()
            );
            foreach ($arr as $model) {
                $app['models'][] = $model;
            }
            $models[] = $app;
        }

        return $this->e->twig->render('admin/dash.html', array(
            'models' => $models
        ));

    }
}

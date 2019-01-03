<?php
/**
 * 
 */
namespace Light\Container;

/**
 * This class will contain all dependencies objects
 */
class Container
{
    /**
     * Instances is sharable
     * 
     * @var array
     */
    protected $instances = [];

    /**
     * Aliases are registed
     * 
     * @var array
     */
    protected $aliases = [];

    /**
     * Instances are resolved
     * 
     * @var array
     */
    protected $resolved = [];

    /**
     * Bind struct of instances
     * 
     * @var array
     */
    protected $bindings = [];

    /**
     * The contextual binding map.
     *
     * @var array
     */
    public $contextual = [];

    /**
     * The stack of concretions being current built.
     *
     * @var array
     */
    protected $buildStack = [];

    /**
     * This function will bind object to alias
     * 
     * @param string|array $abstract
     * @param Closure $concrete - object need to binding
     * @param boolean $shared - This is singtone object or not
     * 
     * @return void
     */
    public function bind($abstract, $concrete = null, $shared = false)
    {
        $this->bindings[$abstract] = compact('concrete', 'shared');

        $this->alias($abstract, $alias = '');
    }

    public function alias($abstract, $alias)
    {
        $this->aliases[$alias] = $abstract;
    }


    public function make($abstract, $parameters = [])
    {
        if (!empty($this->instances) && isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->getConcrete($abstract);
        if ($this->isBuildable($concrete, $abstract)) {
            $object = $this->build($concrete, $parameters);
        } else {
            $object = $this->make($concrete, $parameters);
        }

        if ($this->isShared($abstract)) $this->instances[$abstract] = $object;

        return $object;
    }

    /**
     * Determine if a given type is shared.
     *
     * @param  string  $abstract
     * @return bool
     */
    public function isShared($abstract)
    {
        if (@$this->bindings[$abstract]['shared']) {
            $shared = $this->bindings[$abstract]['shared'];
        } else {
            $shared = false;
        }

        return @$this->instances[$abstract] || $shared === true;
    }

    /**
     * Get the concrete type for a given abstract.
     *
     * @param  string  $abstract
     * @return mixed   $concrete
     */
    protected function getConcrete($abstract)
    {
        if ( ! is_null($concrete = $this->getContextualConcrete($abstract)))
        {
            return $concrete;
        }

        // If we don't have a registered resolver or concrete for the type, we'll just
        // assume each type is a concrete name and will attempt to resolve it as is
        // since the container should be able to resolve concretes automatically.
        if (!@$this->bindings[$abstract])
        {
            if ($this->missingLeadingSlash($abstract) &&
                isset($this->bindings['\\'.$abstract]))
            {
                $abstract = '\\'.$abstract;
            }

            return $abstract;
        }

        return $this->bindings[$abstract]['concrete'];
    }

    /**
     * Get the contextual concrete binding for the given abstract.
     *
     * @param  string  $abstract
     * @return string
     */
    protected function getContextualConcrete($abstract)
    {
        if (isset($this->contextual[end($this->buildStack)][$abstract]))
        {
            return $this->contextual[end($this->buildStack)][$abstract];
        }
    }

    /**
     * Determine if the given concrete is buildable.
     *
     * @param  mixed   $concrete
     * @param  string  $abstract
     * @return bool
     */
    protected function isBuildable($concrete, $abstract)
    {
        return $concrete === $abstract || $concrete instanceof Closure;
    }

    /**
     * Instantiate a concrete instance of the given type.
     *
     * @param  string  $concrete
     * @param  array   $parameters
     * @return mixed
     *
     * @throws BindingResolutionException
     */
    public function build($concrete, $parameters = [])
    {
        // If the concrete type is actually a Closure, we will just execute it and
        // hand back the results of the functions, which allows functions to be
        // used as resolvers for more fine-tuned resolution of these objects.
        if ($concrete instanceof \Closure)
        {
            return $concrete($this, $parameters);
        }

        $reflector = new \ReflectionClass($concrete);

        // If the type is not instantiable, the developer is attempting to resolve
        // an abstract type such as an Interface of Abstract Class and there is
        // no binding registered for the abstractions so we need to bail out.
        if ( ! $reflector->isInstantiable())
        {
            $message = "Target [concrete] is not instantiable.";

            throw new \Exception($message);
        }

        $this->buildStack[] = $concrete;

        $constructor = $reflector->getConstructor();

        // If there are no constructors, that means there are no dependencies then
        // we can just resolve the instances of the objects right away, without
        // resolving any other types or dependencies out of these containers.
        if (is_null($constructor))
        {
            array_pop($this->buildStack);

            return new $concrete;
        }

        $dependencies = $constructor->getParameters();

        // Once we have all the constructor's parameters we can create each of the
        // dependency instances and then use the reflection instances to make a
        // new instance of this class, injecting the created dependencies in.
        $parameters = $this->keyParametersByArgument(
            $dependencies, $parameters
        );

        $instances = $this->getDependencies(
            $dependencies, $parameters
        );

        array_pop($this->buildStack);

        return $reflector->newInstanceArgs($instances);
    }

    /**
     * If extra parameters are passed by numeric ID, rekey them by argument name.
     *
     * @param  array  $dependencies
     * @param  array  $parameters
     * @return array
     */
    protected function keyParametersByArgument(array $dependencies, array $parameters)
    {
        foreach ($parameters as $key => $value)
        {
            if (is_numeric($key))
            {
                unset($parameters[$key]);

                $parameters[$dependencies[$key]->name] = $value;
            }
        }

        return $parameters;
    }


    /**
     * Resolve all of the dependencies from the ReflectionParameters.
     *
     * @param  array  $parameters
     * @param  array  $primitives
     * @return array
     */
    protected function getDependencies($parameters, array $primitives = [])
    {
        $dependencies = [];

        foreach ($parameters as $parameter)
        {
            $dependency = $parameter->getClass();

            // If the class is null, it means the dependency is a string or some other
            // primitive type which we can not resolve since it is not a class and
            // we will just bomb out with an error since we have no-where to go.
            if (array_key_exists($parameter->name, $primitives))
            {
                $dependencies[] = $primitives[$parameter->name];
            }
            elseif (is_null($dependency))
            {
                $dependencies[] = $this->resolveNonClass($parameter);
            }
            else
            {
                $dependencies[] = $this->resolveClass($parameter);
            }
        }

        return (array) $dependencies;
    }

    /**
     * Determine if the given abstract has a leading slash.
     *
     * @param  string  $abstract
     * @return bool
     */
    protected function missingLeadingSlash($abstract)
    {
        return is_string($abstract) && strpos($abstract, '\\') !== 0;
    }
}
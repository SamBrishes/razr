Harx Docs - @get( )
===================

The get function allows you to receive a global variable and use it within block-scoped context,
without changing the outer-scoped version. Using the `AS` statement you can also map the outer
scoped variable to a local scoped one, as shown on the second example. Of course the new assigned
variable is not available on the ouside scope.


Example
-------

```
@set( $count = 0 )

@block( "counter" )
    @get( $count )

    @set( $count++ )
    @( $count )         // Prints 1

    @set( $count++ )
    @( $count )         // Prints 2

    @set( $count++ )
    @( $count )         // Prints 3
@endblock( )

@( $count )     // Prints 0
```


Map the variable to another one.

```
@set( $string = "Hello World" )

@block( "counter" )
    @get( $string AS $greeting )
    @set( $greeting .= ", how are you?" )

    @( $greeting )      // Prints "Hello World, how are your?""
@endblock( )

@( $string )        // Prints "Hello World"
@( $greeting )      // Throws an Exception, because $greeting is not degined here.
```


See Also
--------

- [@set( )](@set)

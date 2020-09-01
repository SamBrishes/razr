Harx Docs - @set( )
===================

The set function allows you to define new or change existing variables, which gets all set in the
global environment space, and thus can be used by all files within the same LoaderInterface
instance.


Example
-------

```
@set( $variable = "Hello World" )       // Sets the new global $variable with the content "Hello World"
@set( $count = 0 )                      // Sets the new global $count with the content 0
@set( $count++ )                        // Change the global $count to 1
```


See Also
--------

- [@get( )](@get)

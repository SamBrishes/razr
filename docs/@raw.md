Harx Docs - @raw( )
===================

The `@raw( )` function directly prints an unescaped version of the inner content. You can use
variables, strings but also calculations and any other kind of printable statements here.


Example
-------

```
@( "<div>This HTML element gets printed using the htmlspecialchars() PHP function.</div>" )
@( 12 + 44 )
@( $variable )
@( $array.index )
@( $object.method )
```

Turns into

```
<div>This HTML Element gets printed using the htmlspecialchars() PHP function.</div>
56
Variable Content
Array Content
The content within gets returned by the method(), getMethod() or Magic __call() method.
```


See Also
--------

- [@e( )](@e)
- [@escape( )](@escape)
- [@raw( )](@raw)

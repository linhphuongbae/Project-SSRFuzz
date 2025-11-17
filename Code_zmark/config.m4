dnl config.m4 for extension zmark

PHP_ARG_ENABLE(zmark, whether to enable zmark support,
[  --enable-zmark           Enable zmark support],
[ no ])

if test "$PHP_ZMARK" = "yes"; then
    PHP_NEW_EXTENSION(zmark, zmark.c taint.c rename.c opcode.c, $ext_shared)
fi

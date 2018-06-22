<?php

/**
 * `StudlyCase`-ify controllers names.
 *
 * @return array
 * @author Atef Ben Ali
 */
function studlyCaseifyControllersNames()
{
    $controllers_path = app_path() . '\controllers';

    // list all controllers files
    // in the controllers path
    exec(
        'ls '
        . $controllers_path,
        $ls_command_output,
        $ls_command_returned_value
    );

    // filter the un-`StudlyCase`-d
    // controllers names
    $unstudly_cased_controllers_names = array_filter(
        $ls_command_output,
        function ($v) {
            return $v !== studly_case($v);
        }
    );

    // `StudlyCase`-ify controllers names
    $studly_cased_controllers_names = array_map(
        function ($v) {
            return studly_case($v);
        },
        $unstudly_cased_controllers_names
    );

    $mv_command_output = array();

    // change un-`StudlyCase`-d controllers name
    // to `StudlyCase`-d controllers names
    foreach (
        $unstudly_cased_controllers_names as $key => $name
    ) {
        // after moving the controller file,
        // the controller class
        // name must be replaced with
        // `StudlyCase`-d class name.

        // those two instructions may return booleans
        // if there's an error
        // @todo deal with the error case
        $controller_file_contents = @file_get_contents(
            $controllers_path
            . '\\'
            . $name
        );

        $file_putted_contents = @file_put_contents(
            $controllers_path
            . '\\'
            . $name,
            str_replace(
                str_replace(
                    '.php',
                    '',
                    $name
                ),
                str_replace(
                    '.php',
                    '',
                    $studly_cased_controllers_names[$key]
                ),
                $controller_file_contents
            )
        );

        exec(
            'mv -v '
            . $controllers_path
            . '\\'
            . $name
            . ' '
            . $controllers_path
            . '\\'
            . $studly_cased_controllers_names[$key],
            $mv_command_output,
            $mv_command_returned_value
        );
    }

    return $mv_command_output;
}

/**
 * `camelCase`-ify functions names.
 *
 * @return void
 * @author Atef Ben Ali
 */
function camelCaseifyFunctionsNames()
{
    $controllers_path = app_path() . '\controllers';

    exec(
        'ls '
        . $controllers_path,
        $ls_command_output,
        $ls_command_returned_value
    );

    // @todo call `studlyCaseifyControllersNames` function

    foreach (
        $ls_command_output as $key => $name
    ) {
        // those two instructions may return booleans
        // if there's an error
        // @todo deal with the error case
        $controller_file_contents = @file_get_contents(
            $controllers_path
            . '\\'
            . $name
        );

        $pattern = "/function\s[a-zA-Z0-9_]*()/";

        preg_match_all(
            $pattern,
            $controller_file_contents,
            $matches
        );

        foreach (
            $matches[0] as $function_name
        ) {
            $controller_file_contents = str_replace(
                str_replace(
                    'function ',
                    '',
                    $function_name
                ),
                camel_case(
                    str_replace(
                        'function ',
                        '',
                        $function_name
                    )
                ),
                $controller_file_contents
            );
        }

        $file_putted_contents = @file_put_contents(
            $controllers_path
            . '\\'
            . $name,
            $controller_file_contents
        );
    }
}

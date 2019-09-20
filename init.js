#!/bin/bash
PS3='Please enter your choice: '
options=(
    "Show production log"
    "Remove production log"
    "Doctrine schema validate"
    "Doctrine schema update"
    "Symfony cache:clear production"


    "Quit"
    )
select opt in "${options[@]}"
do
    case $opt in
        "Show production log")
            echo "Show production log"
            vim "var/logs/prod.log"
        ;;

        "Remove production log")
            echo "You chose remove production log"
            rm "var/logs/prod.log"
        ;;

        "Doctrine schema validate")
            echo "You chose doctrine schema validate"
            php bin/console doctrine:schema:validate
        ;;

        "Doctrine schema update")
            echo "You chose doctrine schema update"
            php bin/console doctrine:schema:update --force
        ;;

        "Symfony cache:clear production")
            echo "You chose Symfony cache:clear production"
            php bin/console cache:clear --env=prod
        ;;


        "Quit")
            break
            ;;
        *) echo "invalid option $REPLY";;
    esac
done

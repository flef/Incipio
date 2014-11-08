#
#   This file is part of Incipio.
#
#   Incipio is an enterprise resource planning for Junior Enterprise
#   Copyright (C) 2012-2014 Florian Lefevre.
#
#   Incipio is free software: you can redistribute it and/or modify
#   it under the terms of the GNU Affero General Public License as
#   published by the Free Software Foundation, either version 3 of the
#   License, or (at your option) any later version.
#
#   Incipio is distributed in the hope that it will be useful,
#   but WITHOUT ANY WARRANTY; without even the implied warranty of
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#   GNU Affero General Public License for more details.
#
#   You should have received a copy of the GNU Affero General Public License
#   along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.
#


LANG=fr_FR.ANSI COPYRIGHT_PHP="\
/*\n\
This file is part of Incipio.\n\
\n\
Incipio is an enterprise resource planning for Junior Enterprise\n\
Copyright (C) 2012-2014 Florian Lefevre.\n\
\n\
Incipio is free software: you can redistribute it and/or modify\n\
it under the terms of the GNU Affero General Public License as\n\
published by the Free Software Foundation, either version 3 of the\n\
License, or (at your option) any later version.\n\
\n\
Incipio is distributed in the hope that it will be useful,\n\
but WITHOUT ANY WARRANTY; without even the implied warranty of\n\
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n\
GNU Affero General Public License for more details.\n\
\n\
You should have received a copy of the GNU Affero General Public License\n\
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.\n\
*/\n"

LANG=fr_FR.ANSI COPYRIGHT_YML="\n\
#\n\
#   This file is part of Incipio.\n\
#\n\
#   Incipio is an enterprise resource planning for Junior Enterprise\n\
#   Copyright (C) 2012-2014 Florian Lefevre.\n\
#\n\
#   Incipio is free software: you can redistribute it and/or modify\n\
#   it under the terms of the GNU Affero General Public License as\n\
#   published by the Free Software Foundation, either version 3 of the\n\
#   License, or (at your option) any later version.\n\
#\n\
#   Incipio is distributed in the hope that it will be useful,\n\
#   but WITHOUT ANY WARRANTY; without even the implied warranty of\n\
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n\
#   GNU Affero General Public License for more details.\n\
#\n\
#   You should have received a copy of the GNU Affero General Public License\n\
#   along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.\n\
#\n"

LANG=fr_FR.ANSI COPYRIGHT_TWIG="\n\
{#\n\
This file is part of Incipio.\n\
\n\
Incipio is an enterprise resource planning for Junior Enterprise\n\
Copyright (C) 2012-2014 Florian Lefevre.\n\
\n\
Incipio is free software: you can redistribute it and/or modify\n\
it under the terms of the GNU Affero General Public License as\n\
published by the Free Software Foundation, either version 3 of the\n\
License, or (at your option) any later version.\n\
\n\
Incipio is distributed in the hope that it will be useful,\n\
but WITHOUT ANY WARRANTY; without even the implied warranty of\n\
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the\n\
GNU Affero General Public License for more details.\n\
\n\
You should have received a copy of the GNU Affero General Public License\n\
along with Incipio as the file LICENSE.  If not, see <http://www.gnu.org/licenses/>.\n\
#}\n"



echo '\nTraitement des fichiers yml :\n'

for file in $(find src/ app/Resources/ -name '*.yml')
do 
    if ! grep -q Copyright $file
    then
        echo YML file : $file
        LANG=fr_FR.UTF-8 sed -i "1i\\
        $COPYRIGHT_YML" $file
    fi
done



echo '\nTraitement des fichiers php :\n'

for file in $(find src/ app/Resources/ -name '*.php')
do
    if ! grep -q Copyright $file
    then
        echo PHP file : $file
        LANG=fr_FR.UTF-8 sed -i "/<?php/a\\
        \n$COPYRIGHT_PHP" $file
    fi
done



echo '\nTraitement des fichiers css :\n'

for file in $(find src/ app/Resources/ -name '*.css')
do
    if ! grep -q Copyright $file
    then
        echo PHP file : $file
        LANG=fr_FR.UTF-8 sed -i "1i\\
        $COPYRIGHT_PHP" $file
    fi
done



echo '\nTraitement des fichiers js :\n'

for file in $(find src/ app/Resources/ -name '*.js')
do
    if ! grep -q Copyright $file
    then
        echo PHP file : $file
        LANG=fr_FR.UTF-8 sed -i "1i\\
        $COPYRIGHT_PHP" $file
    fi
done



echo '\nTraitement des fichiers twig :\n'

for file in $(find src/ app/Resources/ -name '*.twig');
do
    if ! grep -q Copyright $file
    then
        echo TWIG file : $file
        LANG=fr_FR.UTF-8 sed -i "1i\\
        $COPYRIGHT_TWIG" $file
    fi
done



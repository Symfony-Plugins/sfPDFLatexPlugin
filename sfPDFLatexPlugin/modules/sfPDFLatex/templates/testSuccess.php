<?php use_helper('Latex', 'Date') ?>
\documentclass[fontsize=11pt]{scrlttr2}
\LoadLetterOption{DIN}
\usepackage[latin1]{inputenc}
\usepackage{palatino}
\usepackage{eurosym}
\usepackage{ngerman}
\usepackage{calc}
\usepackage{eso-pic}
\usepackage{graphicx}
\usepackage[text={21cm, 29.7cm}, left=2cm, right=1.7cm]{geometry}

\providecommand{\LenToUnit}[1]{#1\@gobble}
\newlength{\wpXoffset}
\setlength{\wpXoffset}{-\hoffset}
\newlength{\wpYoffset}
\setlength{\wpYoffset}{0pt}

\newcommand{\ThisCenterWallPaper}[2]{%
\AddToShipoutPicture*{\put(\LenToUnit{\wpXoffset},\LenToUnit{\wpYoffset}){%
     \parbox[b][\paperheight]{\paperwidth}{%       
       \vfill
       \centering
       \includegraphics[width=#1\paperwidth,height=#1\paperheight,%
                        keepaspectratio]{#2}%
       \vfill
     }}
  }
}  
\ThisCenterWallPaper{1.0}{<?= $this->getDirectory()."/firm.pdf"?>}

\begin{document}
\begin{letter}
{Herr\\
 Musterman\\
  Musterstr. 123\\
 99999 Musterhausen}
\opening{\textbf{Congratulation}}

Hello world,
this is \LaTeX.\\
You have successfully installed the sfPDFLatexPlugin.\\

\closing{With regards from Dresden, Germany}
\end{letter}
%%\newpage
\end{document}

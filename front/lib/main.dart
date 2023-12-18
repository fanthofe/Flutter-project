import 'package:flutter/material.dart';
import 'package:obaby_front/core/color_theme.dart';
import 'package:obaby_front/core/router.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp.router(
      title: 'O\'Baby',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: ColorTheme.darkBlue),
        canvasColor: ColorTheme.yellow,
        useMaterial3: true,
        fontFamily: 'helvetica',
      ),
      // routerConfig: AppRouter.getRouter(context),
      routerConfig: AppRouter.router,
    );
  }
}
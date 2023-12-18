import 'package:go_router/go_router.dart';
// import 'package:flutter/widgets.dart';
import 'package:obaby_front/views/list_profils_view.dart';


abstract class AppRouter {

  // static final _publicRoutes = <String>[
  //   '/login'
  // ];

static GoRouter router = GoRouter(
  // static GoRouter getRouter(BuildContext context) => GoRouter(
    // initialLocation: '/login', 
    // refreshListenable:
    // GoRouterRefreshStream(context.read<AuthenticationCubit>().stream),
    // redirect: (context, state){
    //   final status = context.read<AuthenticationCubit>().state;
    //   if(_publicRoutes.contains(state.location) && status is AuthenticationStateAuthenticated){
    //     return '/';
    //   } 
      
    //   if (!_publicRoutes.contains(state.location) && status is AuthenticationStateUnauthenticated){
    //     return '/login';
    //   }
    //   return null;
    // },
    initialLocation: '/',
    routes: [
    GoRoute(
      path: '/',
      name: ListProfilsView.pageName,
      builder: (context, state) => const ListProfilsView(),
    ),
  ]);
}